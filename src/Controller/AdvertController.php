<?php

namespace App\Controller;

use ApiPlatform\State\Pagination\Pagination;
use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\ApiPlatform\PatchFormatsConfig;

#[Route('/admin/advert')]
final class AdvertController extends AbstractController
{
    #[Route(name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $limit = 1;
        $query = $advertRepository->createQueryBuilder('advert')
            ->select('advert', 'COUNT(pictures.id) AS photo_count')
            ->leftJoin('advert.pictures', 'pictures')
            ->groupBy('advert.id')
            ->orderBy('advert.createdAt', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()->setFirstResult($offset)->setMaxResults($limit);

        $previous = $offset - $limit;
        $next = min(count($paginator),$offset + $limit);

        return $this->render('advert/index.html.twig', [
            'adverts' => $paginator,
            'previous' => $previous >= 0 ? $previous : null,
            'next' => $next < count($paginator) ? $next : null,
        ]);
    }

    #[Route('/new', name: 'app_advert_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_advert_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_delete', methods: ['POST'])]
    public function delete(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/publish', name: 'app_advert_publish', methods: ['POST'])]
    public function publish(Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $advert->setState('published');
        $entityManager->flush();
        return $this->redirectToRoute('app_advert_index');
    }

    #[Route('/{id}/reject', name: 'app_advert_reject', methods: ['POST'])]
    public function reject(Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $advert->setState('rejected');
        $entityManager->flush();
        return $this->redirectToRoute('app_advert_index');
    }
}
