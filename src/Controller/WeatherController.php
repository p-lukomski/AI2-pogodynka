<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherController extends AbstractController
{
    #[Route('/weather/{city}', name: 'app_weather_city', requirements: ['city' => '[^/]+'])]
    #[Route('/weather/{city}/{country}', name: 'app_weather_city_country', requirements: ['city' => '[^/]+', 'country' => '[A-Za-z]{2}'])]
    public function city(
        string $city, ?string $country, LocationRepository $locations, MeasurementRepository $measurementsRepo): Response {
        $city = trim($city);
        $country = $country ? strtoupper($country) : null;

        $location = $locations->findByCityAndCountry($city, $country);
        if (!$location) {
            throw $this->createNotFoundException("Location '{$city}'".($country ? ", {$country}" : '')." not found");
        }

        $measurements = $measurementsRepo->findByLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }

}
