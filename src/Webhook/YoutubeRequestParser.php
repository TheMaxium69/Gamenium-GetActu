<?php

namespace App\Webhook;

use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class YoutubeRequestParser extends AbstractRequestParser
{

    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }
    
    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            // Add RequestMatchers to fit your needs
        ]);
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        if ($request->getMethod() === 'GET') {
            $challenge = $request->query->get('hub_challenge') ?? '';
            if ($challenge) {
                // Retourne le challenge pour vérifier le webhook
                $response = new Response($challenge);
                $response->send();
                exit;
            }
        }

        
        // Requête POST pour les notifications
        if ($request->getMethod() === 'POST') {
            $content = $request->getContent();

            try {
                $xml = new \SimpleXMLElement($content);
            } catch (\Exception $e) {
                // Si le XML est invalide
                throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Invalide XML');
            }

            // Extrait les infos de la notif pour le consumer
            $videoId = str_replace('yt:video:', '', (string) $xml->entry->id);
            $author = (string) $xml->entry->author->name ?? null;
            $channelUri = (string) $xml->entry->author->uri ?? null;

            // Retourne un RemoteEvent pour les notifications
            return new RemoteEvent('youtube', $author, [
                'channel_uri' => $channelUri,
                'video_id' => $videoId
            ]);
        }

        throw new RejectWebhookException(Response::HTTP_METHOD_NOT_ALLOWED, 'Method Invalide');
        
    }
}
