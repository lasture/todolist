<?php

namespace AppBundle\Controller;



use AppBundle\Exception\ResourceValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Article;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Nelmio\ApiDocBundle\Annotation as Doc;



class ArticleController extends FOSRestController
{

    # Retourner une article par id :: GET
    /**
     * @Rest\Get(
     *     path = "/articles/{id}",
     *     name = "api_get",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *     section="Articles",
     *     resource=true,
     *     description="Get one article.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The article unique identifier."
     *         }
     *     }
     * )
     * @Rest\View
     */
    public function getArticleByIdAction($id)
    {
      $restresult = $this->getDoctrine()->getRepository('AppBundle:Article')->find($id);
        if ($restresult === null) {
          return new Response("this article is not exist", Response::HTTP_NOT_FOUND);
     }
        return $restresult;
    }

    # Retourner la liste des articles :: GET
    /**
     * @Rest\Get("/articles", name="api_getArticles")
     * @Doc\ApiDoc(
     *     section="Articles",
     *     resource=true,
     *     description="Get the list of all articles."
     * )
     * @Rest\View()
     */
    public function listAction()
    {
    	

        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();
        
        return $articles;
    }

    # Ajouter un nouveau article :: POST
    /**
     * @Rest\Post(
     *     path = "/articles",
     *     name = "api_postArticle"
     * )
     * @ParamConverter(
     *     "article",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="post" }
     *     }
     * )
     * @Doc\ApiDoc(
     *     section="Articles",
     *     description="Add an articles.",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Rest\View(StatusCode = 201)
     */
    public function postArticleAction(Article $article, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

    	$em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->view($article, Response::HTTP_CREATED, ['Location' => $this->generateUrl('api_getArticles', ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    # Supprimer une article ::DELETE
    /**
     * @Rest\Delete(
     *     path = "/articles/{id}",
     *     name = "api_delete",
     *     requirements = {"id"="\d+"}
     *      )
     * @Doc\ApiDoc(
     *     section="Articles",
     *     description="Delete one articles.",
     *     statusCodes={
     *         202="Returned when accepted",
     *         404="Returned when an article not found"
     *     }
     * )
     * @Rest\View()
     */
    public function upadateAction($id)
    {
        $article = $this->getDoctrine()->getRepository('AppBundle:Article')->find($id);

        if ($article === null) {
            return new Response("Article not exist", Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return new Response("Delete article with succes", Response::HTTP_ACCEPTED);
    }

    # Mettre à jour un ou plusieurs attributs du table article
    /**
     * @Rest\Put(
     *     path = "/articles/{id}",
     *     name = "api_put",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *     section="Articles",
     *     resource=true,
     *     description="Update one article.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The article unique identifier."
     *         }
     *     }
     * )
     * @Rest\View()
     */
    public function updateAction(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $em = $this->getDoctrine()->getManager();
        $article = $this->getDoctrine()->getRepository('AppBundle:Article')->find($request->get('id'));
        if (empty($article)) {
            return new Response("Article not found", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($title) && !empty($content)){
            $article->setTitle($title);
            $article->setContent($content);
            $em->merge($article);
            $em->flush();
            return new Response("Article Updated Successfully", Response::HTTP_OK);
        }
        elseif(empty($title) && !empty($content)){
            $article->setContent($content);
            $em->merge($article);
            $em->flush();
            return new Response("Article Updated Successfully", Response::HTTP_OK);
        }
        elseif(!empty($title) && empty($content)){
            $article->setTitle($title);
            $em->merge($article);
            $em->flush();
            return new Response("Article Updated Successfully", Response::HTTP_OK);
        }
        else
            return new Response("Article title or content cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

}