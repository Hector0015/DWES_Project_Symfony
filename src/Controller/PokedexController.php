<?php 
namespace App\Controller;

use App\Entity\Pokemon;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class PokedexController extends AbstractController{
    private $pokemons = [
        1 => ["nombre" => "Bulbasaur", "numero" => "0001", "Tipo" => "planta/veneno"],


    	8 => ["nombre" => "Wartortle", "numero" => "0008", "Tipo" => "agua"],


    	6 => ["nombre" => "Charizard", "numero" => "0006", "Tipo" => "fuego/volador"],


    	23 => ["nombre" => "Ekans", "numero" => "0023", "Tipo" => "veneno"],


    	15 => ["nombre" => "Beedril", "numero" => "0015", "Tipo" => "bicho/veneno"]

    ];

    #[Route('/pokedex/insertar', name:'insertar_pokemon')]
        public function insertar(ManagerRegistry $doctrine){
            $entityManager = $doctrine->getManager();
            foreach($this->pokemons as $c){
                $pokemon = new Pokemon();
                $pokemon->setNombre($c["nombre"]);
                $pokemon->setNumero($c["numero"]);
                $pokemon->setTipo($c["Tipo"]);
                $entityManager->persist($pokemon);
            }

            try{
                $entityManager->flush();
                return new Response("Pokemon insertado");
            }catch (\Exception $e) {
                return new Response("Error insertando objetos");
            }
        }

    #[Route('/pokedex/{codigo}', name:"ficha_pokedex")]
        public function ficha($codigo): Response{
            $resultado = ($this->pokemons[$codigo] ?? null);

            return $this->render('ficha_pokedex.html.twig', [
            'pokemon' => $resultado
            ]);
            
        }

    #[Route("/pokedex/buscar/{texto}", name:"buscar_pokemon")]
        public function buscar($texto): Response{
            $resultados = array_filter($this->pokemons,
            function ($pokemons) use ($texto){
                return strpos($pokemons["nombre"], $texto) !== FALSE;
            }
        );

        return $this->render('lista_pokemons.html.twig', [
            'pokemons' => $resultados
        ]);
            
        }

        }
    
?>