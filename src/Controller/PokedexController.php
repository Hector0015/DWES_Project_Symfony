<?php 
namespace App\Controller;

use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PokedexController extends AbstractController{
    private $pokemons = [
        1 => ["nombre" => "Bulbasaur", "numero" => "0001", "Tipo" => "planta/veneno"],


    	8 => ["nombre" => "Wartortle", "numero" => "0008", "Tipo" => "agua"],


    	6 => ["nombre" => "Charizard", "numero" => "0006", "Tipo" => "fuego/volador"],


    	23 => ["nombre" => "Ekans", "numero" => "0023", "Tipo" => "veneno"],


    	15 => ["nombre" => "Beedril", "numero" => "0015", "Tipo" => "bicho/veneno"]

    ];
    #[Route('/pokedex/{codigo}', name:"ficha_pokedex")]
        public function ficha($codigo): Response{
            $resultado = ($this->pokemons[$codigo] ?? null);

            if($resultado){
                $html = "<ul>";
                    $html .= "<li>" . $codigo . "</li>";
                    $html .= "<li>" . $resultado['nombre'] . "</li>";
                    $html .= "<li>" . $resultado['numero'] . "</li>";
                    $html .= "<li>" . $resultado['Tipo'] . "</li>";
                $html .= "</ul>";
                return new Response("<html><body>$html</body>");
            }else
            return new Response("<html><body>Pokemon $codigo no encontrado</body>");
        }

    #[Route("/pokedex/buscar/{texto}", name:"buscar_pokemon")]
        public function buscar($texto): Response{
            $resultados = array_filter($this->pokemons,
            function ($pokemons) use ($texto){
                return strpos($pokemons["nombre"], $texto) !== FALSE;
            }
        );

        if (count($resultados)){
            if($resultados){
                $html = "<ul>";
                foreach($resultados as $id => $resultado){
                    $html .= "<li>" . $id . "</li>";
                    $html .= "<li>" . $resultado['nombre'] . "</li>";
                    $html .= "<li>" . $resultado['numero'] . "</li>";
                    $html .= "<li>" . $resultado['Tipo'] . "</li>";
                }
                $html .= "</ul>";
                return new Response("<html><body>$html</body>");
            }else
            return new Response("<html><body>No se encontró ningún pokemon</body>");
        }

        }
    }
?>