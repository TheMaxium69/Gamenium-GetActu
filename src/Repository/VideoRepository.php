<?php

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\Video;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    private string $userId;

    public function __construct(ManagerRegistry $registry, private ParameterBagInterface $params)
    {
        parent::__construct($registry, Video::class);
    }

    public function sendToGameniumApi(Video $video, Channel $channel) {
        $dbHost = $this->params->get('gamenium_db_host');
        $dbName = $this->params->get('gamenium_db_name');
        $dbUser = $this->params->get('gamenium_db_user');
        $dbPassword = $this->params->get('gamenium_db_password');

        // connect to gamenium back bdd
        $db = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPassword);

        // ajouter la thumbnail dans la table picture
        $picture = $db->prepare('INSERT INTO picture (`user_id`, `url`, `posted_at`, `ip`) VALUES (:user, :thumburl, :post_date, :ip)');
        $picture->execute([
            'user'=> 1,
            'thumburl'=> $video->getThumbnailUrl(),
            'post_date'=> 'test',
            'ip'=> '10.10.10.10',
        ]);

        // id de la nouvelle picture
        $pictureId = $db->lastInsertId();

        // chercher le provider dans la table provider et récupérer l'id
        $users = $db->prepare('SELECT * FROM `provider`');
        $users->execute();
        $result = $users->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $user) {
            if (str_contains($channel->getName(), $user['display_name'])) {
                $this->userId = $user['id'];
                break;
            }
        }

        // si no user, on ajoute ???

        // prepare sql stmt new post actu
        $stmt = $db->prepare('INSERT INTO post_actu (`provider_id`, `user_id`, `picture_id`, `created_at`, `content`, `title`) VALUES (:providerId, :user, :picture, :creation_date, :content, :title)');
        
        // execute new post actu
        $stmt->execute([
            'providerId'=> $this->userId,
            'user' => 1,
            'picture'=> $pictureId,
            'creation_date'=> 'test',
            'content'=> $video->getDescription(),
            'title'=> $video->getTitle()
        ]);

    }

    //    /**
    //     * @return Video[] Returns an array of Video objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Video
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
