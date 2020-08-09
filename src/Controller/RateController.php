<?php
    
namespace App\Controller;
    
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rate;
    
class RateController extends AbstractController
{
    /**
     * Shows all exchange rates ordered by publishing date
     *
     * @Route("/rates", name="rates")
     */
    public function getRates()
    {
        $rates = $this->getDoctrine()
        ->getRepository(Rate::class)
        ->findBy([], ['published_on' => 'DESC']);

        if (!$rates) {
            throw $this->createNotFoundException(
                'No rates currently found in database.'
            );
        }

         foreach($rates as $item) {
            $arrayCollection[] = array(
                'id' => $item->getId(),
                'country_code' => $item->getCountryCode(),
                'rate' => $item->getRate(),
                'published_on' => $item->getPublishedOn()
            );
       }

        $response = new JsonResponse($arrayCollection);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * Shows exchange rate history for one currency
     *
     * @param string $countryCode
     * 
     * @Route("/rate/{countryCode}", name="singleRate")
     */
    public function getRate(string $countryCode)
    {
        $rates = $this->getDoctrine()
        ->getRepository(Rate::class)
        ->findBy(array('country_code' => $countryCode), array('published_on' => 'DESC'), 3, 1);

        if (!$rates) {
            throw $this->createNotFoundException(
                'No rates currently found in database.'
            );
        }

         foreach($rates as $item) {
            $arrayCollection[] = array(
                'id' => $item->getId(),
                'country_code' => $item->getCountryCode(),
                'rate' => $item->getRate(),
                'published_on' => $item->getPublishedOn()
            );
       }

        $response = new JsonResponse($arrayCollection);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @Route("/", name="ratetable")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }
}
