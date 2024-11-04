<?php

namespace App\Http\Controllers;

use App\Exports\BeerExport;
use App\Http\Requests\BeerRequest;
use App\Services\PunkapiService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BeerController extends Controller
{
    public function index(BeerRequest $request, PunkapiService $service) 
    {
        // $service = new PunkapiService(); Ao invez disso usa-se injeção de dependência.
       
        return $service->getBeers(...$request->validated());
    }

    public function export(BeerRequest $request, PunkapiService $service)
    {

        // Busca as cervejas
        $beers = $service->getBeers(...$request->validated());

        // Filtra as cervejas
        $filteredBeers = collect($beers)->map(function($value, $key) {
            return collect($value)
                ->only(['name', 'tagline', 'first_brewed', 'description'])
                ->toArray();
        })->toArray();

        // MOCK - usa o mock até os caras liberarem a api
        // $params = [
        //     ['name' => 'Bruno', 'age' => 30],
        //     ['name' => 'Carlos', 'age' => 40],
        // ];

        Excel::store(
            new BeerExport($filteredBeers),
            'olw-report.xlsx',
            's3'
        );

        return 'relatório criado';
    }
}
