<?php

namespace App\Controller;

use Add\Entity\Product;
use Add\Repository\CategoryRepository;
use Add\Repository\CRUDRepository;
use App\Entity\CRUD;
use App\Repository\CRUDRepository as RepositoryCRUDRepository;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CRUDController extends AbstractController
{
    #[Route('/crud', name: 'app_c_r_u_d')]
    public function index(Request $request, RepositoryCRUDRepository $CRUDRepository): Response
    {

        $search = $request->query->get("search");
        $searchs = $request->query->get("searchs");
        if ($search === null) {
            $cruds = $CRUDRepository->findAll();
        } else {
            $direction = strtoupper($searchs) === 'DESC' ? 'DESC' : 'ASC';
            $cruds = $CRUDRepository->findBy([], [$search => $direction]);
        }
        return $this->render('crud/index.html.twig', ['cruds' => $cruds]);
    }

    #[Route('/crud', name: 'create_crud', methods: ['POST'])]
    public function  create(EntityManagerInterface $em, Request $request, RepositoryCRUDRepository $CRUDRepository): Response
    {
        $departmentId = $request->request->get('department');
        $category = $CRUDRepository->find($departmentId);
        $crud = new CRUD();
        $crud->setFirstName($request->request->get('first_name'));
        $crud->setLastName($request->request->get('last_name'));
        $crud->setAge($request->request->get('age'));
        $crud->setStatus($request->request->get('Status'));
        $crud->setEmail($request->request->get('email'));
        $crud->setTelegram($request->request->get('telegram'));
        $crud->setAddress($request->request->get('address'));
        $crud->setDepartment($category);
        $em->persist($crud);
        $em->flush();

        return $this->render('/crud');
    }



    #[Route('/crud/create', name: 'create_crud', methods: ["GET"])]
    public function fromCreate(DepartmentRepository $Departmentepository): Response
    {
        $departments =  $Departmentepository->findAll();
        return $this->render('crud/create.html.twig', ["departments" => $departments]);
    }

    #[Route('/crud/{crud}', name: 'delete_crud', methods: ["DELETE"])]
    public function delete(CRUD $crud, EntityManagerInterface $em): Response
    {
        $em->remove($crud);
        $em->flush();

        return $this->redirect('/crud');
    }

    #[Route('/crud/find', name: 'find_crud', methods: ["FIND"])]
    public function find(RepositoryCRUDRepository $CRUDRepository): Response
    {
        $qb = $CRUDRepository->createQueryBuilder('u');
        $qb->andWhere('u.id LIKE :id');
        $qb->setParameter('id', "");


        return $this->redirect('/crud');
    }

    #[Route('/crud/{crud}', name: "edit_crud", methods: ["GET"])]
    public function edit(CRUD $crud, DepartmentRepository $Departmentepository)
    {
        $departments =  $Departmentepository->findAll();
        return $this->render('crud/edit.html.twig', ['crud' => $crud, "departments" => $departments]);
    }


    #[Route('/crud/{crud}', name: "update_crud", methods: ["PUT"])]
    public function update(CRUD $crud, Request $request, EntityManagerInterface $em, RepositoryCRUDRepository $CRUDRepository, DepartmentRepository $departmentRepository) // Inject DepartmentRepository
    {
        $departmentId = $request->request->get('department');
        $department = $departmentRepository->find($departmentId);


        $crud->setFirstName($request->request->get('first_name'));
        $crud->setLastName($request->request->get('last_name'));
        $crud->setAge($request->request->get('age'));
        $crud->setStatus($request->request->get('Status'));
        $crud->setEmail($request->request->get('email'));
        $crud->setTelegram($request->request->get('telegram'));
        $crud->setAddress($request->request->get('address'));
        $crud->setDepartment($department);
        $em->persist($crud);
        $em->flush();



        return $this->redirect('/crud');
    }
}
