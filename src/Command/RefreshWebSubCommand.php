<?php

namespace App\Command;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:refresh:websub',
    description: 'Avoid the expiring pubsubhubbub',
)]
class RefreshWebSubCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
        private ChannelRepository $channelRepository
    )
    {
        parent::__construct();
    }

    // protected function configure(): void
    // {
    //     $this
    //         ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
    //         ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    //     ;
    // }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // SCRIPT REFRESH TOKEN 
        $channels = $this->channelRepository->findAll();
        // Si sur Ngrok, bien refresh l'url a chaque ouverture de port
        $webhookUrl = 'https://9c8a-2001-861-8bb4-f960-cc62-51de-8910-880d.ngrok-free.app'."/webhook/youtube";

        foreach ($channels as $channel) {
            $channelId = $channel->getChannelId();
            $channelName = $channel->getName();

            $response = $this->client->request('POST', 'https://pubsubhubbub.appspot.com/subscribe', [
                'body' => [
                    'hub.callback' => $webhookUrl,
                    'hub.topic' => 'https://www.youtube.com/xml/feeds/videos.xml?channel_id=' . $channelId,
                    'hub.verify' => 'sync',
                    'hub.mode' => 'subscribe'
                ]
            ]);

            if ($response->getStatusCode() === 204) {
                $io->success('Refresh success !');
            } else {
                $io->error('Renouvellement échoué pour ' . $channelName);
            }
        }

        return Command::SUCCESS;
    }
}
