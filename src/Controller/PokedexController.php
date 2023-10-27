<?php 
namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\Region;
use App\Form\PokemonType;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Extension\DumpExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class PokedexController extends AbstractController{
    private $pokemons = [
        1 => ["nombre" => "Bulbasaur", "numero" => "0001", "Tipo" => "planta/veneno"],


    	8 => ["nombre" => "Wartortle", "numero" => "0008", "Tipo" => "agua"],


    	6 => ["nombre" => "Charizard", "numero" => "0006", "Tipo" => "fuego/volador"],


    	23 => ["nombre" => "Ekans", "numero" => "0023", "Tipo" => "veneno"],


    	15 => ["nombre" => "Beedril", "numero" => "0015", "Tipo" => "bicho/veneno"]

    ];

    #[Route('/pokedex/nuevo', name:'nuevo_pokemon')]
    public function nuevo(ManagerRegistry $doctrine, Request $request){
        $pokemon = new Pokemon();

        $formulario = $this->createForm(PokemonType::class, $pokemon);

        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $pokemon = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($pokemon);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_pokedex', ["codigo" => $pokemon->getId()]);
        }
        return $this->render('nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }

    #[Route('/pokedex/editar/{codigo}', name:'editar_pokemon')]
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo) {
        $repositorio = $doctrine->getRepository(Pokemon::class);

        $pokemon = $repositorio->findOneBy(["numero" => $codigo]);

        if($pokemon){
            $formulario = $this->createForm(PokemonType::class, $pokemon);

            $formulario->handleRequest($request);

            if($formulario->isSubmitted() && $formulario->isValid()) {
                $pokemon = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($pokemon);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_pokedex', ["codigo" => $pokemon->getId()]);
            }
            return $this->render('nuevo.html.twig', array(
                'formulario' => $formulario->createView()
            ));
        }else{
            return $this->render('ficha_pokedex.html.twig', [
                'pokemon' => NULL
            ]);
        }
    }


    #[Route('/pokedex/insertarSinRegion', name:'insertar_sin_region_pokemon')]
    public function insertarSinRegion(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Region::class);

        $region = $repositorio->findOneBy(["nombre" => "Kalos"]);

        $pokemon = new Pokemon();

        $pokemon->setNombre(("Insercion de prueba sin provincia, rattata"));
        $pokemon->setNumero("900220023");
        $pokemon->setTipo("Normal");
        $pokemon->setRegion($region);

        $entityManager->persist($pokemon);

        $entityManager->flush();
        return $this->render('ficha_pokedex.html.twig', [
            'pokemon' => $pokemon
        ]);
    }

    #[Route('/pokedex/insertarConRegion', name:'insertar_con_region_pokemon')]
    public function insertarConRegion(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $region = new Region();

        $region->setNombre("Kanto");
        $pokemon = new Pokemon();

        $pokemon->setNombre("Insercion de prueba con region, Pikachu");
        $pokemon->setNumero("900220022");
        $pokemon->setTipo("electrico");
        $pokemon->setRegion($region);

        $entityManager->persist($region);
        $entityManager->persist($pokemon);

        $entityManager->flush();
        return $this->render('ficha_pokedex.html.twig', [
            'pokemon' => $pokemon
        ]);
    }


    #[Route('/pokedex/delete/{id}', name:'eliminar_pokemon')]
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Pokemon::class);
        $pokemon = $repositorio->findOneBy(["numero" => $id]);
        if($pokemon){
            try{
                $entityManager->remove($pokemon);
                $entityManager->flush();
                return new Response("Pokemon eliminado");
            }catch (\Exception $e) {
                return new Response("Error eliminando objeto");
            }
        }else
        return $this->render('ficha_pokedex.html.twig', [
            'pokemon' => null
        ]);
    }


    #[Route('/pokedex/update/{id}', name:'modificar_pokemon')]
    public function update(ManagerRegistry $doctrine, $id, $nombre): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Pokemon::class);
        $pokemon = $repositorio->findOneBy(["numero" => $id]);
        if ($pokemon){
            $pokemon->setNombre($nombre);
            try{
                $entityManager->flush();
                return $this->render('ficha_pokedex.html.twig', [
                    'pokemon' => $pokemon
                ]);
            }catch(\Exception $e) {
                return new Response("Error insertando objetos");
            }
        }else
            return $this->render('ficha_pokedex.html.twig', [
                'pokemon' => null
            ]);
    }

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
        public function ficha(ManagerRegistry $doctrine, $codigo): Response{
            $repositorio = $doctrine->getRepository(Pokemon::class);
            $resultado = $repositorio->findOneBy(["numero" => $codigo]);

            return $this->render('ficha_pokedex.html.twig', [
            'pokemon' => $resultado
            ]);
            
        }

    #[Route("/pokedex/buscar/{texto}", name:"buscar_pokemon")]
        public function buscar(ManagerRegistry $doctrine, $texto): Response{
            $repositorio = $doctrine->getRepository(Pokemon::class);
            $resultados = $repositorio->findByName($texto);
            
        return $this->render('lista_pokemons.html.twig', [
            'pokemons' => $resultados
        ]);
            
        }

        }
    
?>