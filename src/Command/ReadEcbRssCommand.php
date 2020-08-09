<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Rate;

class ReadEcbRssCommand extends Command
{
    protected static $defaultName = 'app:read-ecb-rss';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Gathers the latest exhange rates.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $amountOfCountries = 32;
        $doc = new DOMDocument();
        $doc->load('https://www.bank.lv/vk/ecb_rss.xml');
        foreach ($doc->getElementsByTagName('item') as $node) {
            $itemRSS = array (
                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
            );

            $date = date('Y-m-d', strtotime(substr($itemRSS['date'], 5, 11)));
            $allRates = explode(' ', $itemRSS['desc']);

            for ($i=0; $i<($amountOfCountries*2-1); $i=$i+2){
                $ratesByCountry[] = $allRates[$i] . $allRates[$i+1];
            }

            foreach ($ratesByCountry as $singleRate)
            {
                $countryCode = substr($singleRate, 0, 3);
                $exchangeRate = substr($singleRate, 3);

                $em = $this->entityManager;

                $entry = $em->getRepository(Rate::class)->count(['country_code' => $countryCode, 'published_on' => $date]);

                if ($entry == 0) {
                    $newRate = new Rate();
                    $newRate->setCountryCode($countryCode);
                    $newRate->setRate($exchangeRate);
                    $newRate->setPublishedOn($date);
                    
                    $em->persist($newRate);
                    $em->flush();
                }
            }
            unset($ratesByCountry);
        }

        return Command::SUCCESS;
    }
}