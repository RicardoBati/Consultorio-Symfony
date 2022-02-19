<?php 

namespace App\Controller;

use App\Helper\EntidadeFactory;
use App\Helper\ExtratorDadosRequest;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract Class BaseController extends AbstractController
{
    /**
     * @var ObjectRepository
     */
    protected $repository;
    
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntidadeFactory
     */
    protected $factory;

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $factory,
        ExtratorDadosRequest $extratorDadosRequest
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->extratorDadosRequest = $extratorDadosRequest;
    }

    public function novo (Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $entity = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse($entity);
    }

    public function buscarTodos(Request $request) : Response
    {
        $informacoesDeOrdenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);
        $informacoesDeFiltro = $this->extratorDadosRequest->buscaDadosFiltro($request);
        $lista = $this->repository->findBy($informacoesDeFiltro, $informacoesDeOrdenacao);

        return new JsonResponse($lista);

    }

    public function atualiza(int $id,Request $request) : Response
    {
        $corpoRequisicao = $request->getContent();
        $entidadeEnviada = $this->factory->criarEntidade($corpoRequisicao);

        $entidadeExistente = $this->repository->find($id);
        if (is_null($entidadeExistente)) {
            return new Response('',Response::HTTP_NOT_FOUND);
        }
        $this->atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);

            
        $this->entityManager->flush();

        return new JsonResponse($entidadeExistente);
    }

    public function buscarUm(int $id): Response
    {
        return new JsonResponse($this->repository->find($id));
    }

    public function remove(int $id) : Response
    {
        $entity = $this->repository->find($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    
    abstract public function atualizarEntidadeExistente(
        $entidadeExistente,
        $entidadeEnviada
    );

}

?>