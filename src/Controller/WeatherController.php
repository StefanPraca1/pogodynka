<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    /**
     * /weather/{id}  — wersja po ID (wymagamy liczby)
     */
    #[Route('/weather/{id}', name: 'app_weather', requirements: ['id' => '\d+'])]
    public function city(Location $location, MeasurementRepository $repository): Response
    {
        $measurements = $repository->findByLocation($location);

        return $this->render('weather/city.html.twig', [
            'location'      => $location,
            'measurements'  => $measurements,
        ]);
    }

    /**
     * /weather/{city} lub /weather/{city}/{country}
     * np. /weather/Szczecin  albo  /weather/Szczecin/PL
     */
    #[Route(
        '/weather/{city}/{country}',
        name: 'app_weather_city_country',
        defaults: ['country' => null]
    )]
    public function cityByName(
        string $city,
        ?string $country,
        LocationRepository $locations,
        MeasurementRepository $measurementsRepo
    ): Response {
        $location = $locations->findOneByCityAndCountry($city, $country);

        if (!$location) {
            throw $this->createNotFoundException('Location not found');
        }

        $measurements = $measurementsRepo->findByLocation($location);

        return $this->render('weather/city.html.twig', [
            'location'      => $location,
            'measurements'  => $measurements,
        ]);
    }
}
