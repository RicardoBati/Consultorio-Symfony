<?php 

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    public function buscarTodos() : Response
    {
        $entityList = $this->repository->findAll();

        return new JsonResponse($entityList);

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

}

?>