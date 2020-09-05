<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
//use http\Env\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $users = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findAll();

        return $this->render('user/index.html.twig', ['users' => $users]);
    }


    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request)
    {

        $user = new User(); // cria objeto entity User.
        $form = $this->createForm(UserType::class, $user); // Aqui, criamos o formulário par ser exibido na view
        $form->handleRequest($request); // Para reconhecer se o formulário foi submetido

        if ($form->isSubmitted()){
            $user = $form->getData(); // Recebe os dados do formulário via post.
            $user->setCreatedAt(new \DateTime('now', new \DateTimeZone('America/Recife')));
            $user->setUpdatedAt(new \DateTime('now', new \DateTimeZone('America/Recife')));

            //dump($user); // Esse é um debug do próprio symfony (User é um array com a coleção de dados do formulário.

            // GRAVANDO NO BANCO DE DADOS
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Usuário criado com sucesso!'); // Imprime mensagem para o cliente
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView() // Manda as informações necessárias para a view ser montada com base nesse objeto
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request, $id)
    {
        //Busca repositório populado com base no 'id' indicado
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user); // Aqui, criamos o formulário par ser exibido na view
        $form->handleRequest($request); // Para reconhecer se o formulário foi submetido

        if ($form->isSubmitted()){

            $user = $form->getData(); // Recebe os dados do formulário via post.
            $user->setUpdatedAt(new \DateTime('now', new \DateTimeZone('America/Recife')));

            // ATUALIZANDO NO BANCO DE DADOS
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash('success', 'Usuário editado com sucesso!'); // Imprime mensagem para o cliente
            return $this->redirectToRoute('user_edit', ['id' => $id]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView() // Manda as informações necessárias para a view ser montada com base nesse objeto
        ]);
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id)
    {
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository(User::class)->find($id);

        $manager = $doctrine->getManager(); // Ou... $this->getDoctrine()->getManager()->remove($user);
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', 'Usuário removido com sucesso');

        return $this->redirectToRoute('user_index');
    }
}