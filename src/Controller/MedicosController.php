<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\MedicoFactory;
use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;

class MedicosController extends BaseController
{       

    /**
     * @var MedicoFactory
     */
    private $medicoFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicoRepository $repository
        
    ) {
        parent::__construct($repository, $entityManager);
        $this->medicoFactory = $medicoFactory;
    }

    /**
     *  @Route ("/medicos", methods={"POST"}) 
     */

    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medico = $this->medicoFactory->criarMedico($corpoRequisicao);
        
        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico);
    }


    /**
     *  @Route ("/medicos/{id}", methods={"PUT"}) 
     */

    public function atualiza(int $id,Request $request) : Response
    {
        $corpoRequisicao = $request->getContent();
        $medicoEnviado = $this->medicoFactory->criarMedico($corpoRequisicao);

        $medicoExistente = $this->buscaMedico($id);
        if (is_null($medicoExistente)) {
            return new Response('',Response::HTTP_NOT_FOUND);
        }
 
        $medicoExistente
            ->setCrm($medicoEnviado->getCrm())
            ->setNome($medicoEnviado->getNome());
            
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente);
    }

    /**
     * @Route ("/especialidades/{especialidadeId}/medicos"), methods={GET})
     */

    public function buscarPorEspecialidade(int $especialidadeId) : Response
    {

        $medicos = $this->repository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos);

    }

}



?>