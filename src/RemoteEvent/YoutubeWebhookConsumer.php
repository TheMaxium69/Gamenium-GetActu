<?php

namespace App\RemoteEvent;

use App\Entity\Channel;
use App\Entity\Video;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsRemoteEventConsumer('youtube')]
final class YoutubeWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private ParameterBagInterface $params,
        private EntityManagerInterface $entityManager,
        private ChannelRepository $channelRepository,
        private HttpClientInterface $client
    )
    {}

    public function consume(RemoteEvent $event): void
    {   
        $apiKey = $this->params->get('youtube_api_key');
        $author = $event->getId();
        $content = $event->getPayload();
        $videoId = $content['video_id'];
        $channelUri = $content['channel_uri'];

        if (!$videoId) {
            return;
        }

        // Requete à l'API Youtube pour récupérer les ressources de la vidéo
        $response = $this->client->request('GET', 'https://www.googleapis.com/youtube/v3/videos', [
            'query' => [
                'id' => $videoId,
                'key' => $apiKey,
                'part' => 'snippet,contentDetails,statistics,status',
            ],
        ]);

        $data = $response->toArray();
        $videoData = $data['items'][0];
        // var_dump($videoData);

        // Transfert des infos dans la BDD
        // Verif si la chaîne existe déjà. Si oui, on la stock dans une variable, si non on l'instancie
        $channel = $this->channelRepository->findOneBy(['channelId' => $videoData['snippet']['channelId']]);
        if (!$channel) {
            $channel = new Channel;
            $channel->setName($author)
                    ->setChannelId($videoData['snippet']['channelId']);
    
            $this->entityManager->persist($channel);
        }

        $video = new Video;
        $video->setTitle($videoData['snippet']['title'])
              ->setDescription($videoData['snippet']['description'])
              ->setYtId($videoData['id'])
              ->setThumbnailUrl($videoData['snippet']['thumbnails']['high']['url'])
              ->setPublishTime($videoData['snippet']['publishedAt'])
              ->setChannelId($channel);
        
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }
}
