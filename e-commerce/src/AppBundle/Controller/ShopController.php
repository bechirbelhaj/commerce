<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\AddPan;
use AppBundle\Entity\Commande;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;






class ShopController extends Controller
{
	/**
     * @Route("/Shop", name="Shop")
     */

	 public function shopAction(Request $request){
	 	$em=$this->getDoctrine()->getManager();
	 	$produits=$em->getRepository('AppBundle:Produit')->findAll();

	 	return $this->render('Shop.html.twig',array('produits'=>$produits,));
	 }
	 /**
     * @Route("/details/{id}", name="details")
     */

	 public function detailsAction($id){
	 	$produit=$this->getDoctrine()->getRepository('AppBundle:Produit')->find($id);
	 	return $this->render('details.html.twig',array('produit'=>$produit));
	 }


	 /**
     * @Route("/cart", name="cart")
     */

	public function cartAction(Request $request){
		$token=$this->container->get('security.token_storage')->getToken();
		if(!is_object($token->getUser())){
			return $this->render('cart.html.twig');
		}
		else
		{
			return $this->redirectToRoute('fos_user_profile_show'); /*le nom d'un chemin /profile */
		}
	 }

	 /**
      * @Route("/panier/{id}", name="debu_panier")
      */
public function panierAction($id, Request $request)
{
    $panier= new AddPan();
    $now = new \DateTime('now');
    $produit = $this->getDoctrine()
        ->getRepository('AppBundle:Produit')
        ->find($id);
    //je récupére utilistaur courant
    $token = $this->container->get('security.token_storage')->getToken();
    //si vous n'etes pas connecté, il vous demande de se connecter
    if(!is_object($token->getUser())){
        return $this->redirectToRoute('fos_user_security_login');

    }
    //sinon vous etes redirigé vers la page panier
    else{


             $panier->setDatec($now);
             $panier->setClient($token->getUser());
             $panier->setProduit($produit);
             $form= $this->createFormBuilder($panier)
                    ->add('datec',DateTimeType::class, array('attr' =>array('class' =>'formcontrol')))

                    ->add('client',TextType::class, array('attr' =>array('class' =>'form-control')))
                    ->add('qt',choiceType::class, array('choices' =>array('1' =>'1', '2' =>'2', '3' =>'3'),'attr' =>array('class' =>'form-control')))

                    ->add('Save',SubmitType::class, array('label' =>'Ajouter à mon panier','attr' =>array('class' =>'btn btn-primary')))


                    ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //get data
                $datec = $form['datec']->getData();

                $client = $form['client']->getData();

                $qt = $form['qt']->getData();

                $prixTotal= ($produit->getPrix())*$qt;

                //set data
                $panier->setDatec($datec);
                $panier->setPrixTotal($prixTotal);
                $panier->setClient($client);
                $panier->setQt($qt);
                $panier->setProduit($produit);
                //persit
                $em= $this->getDoctrine()->getManager();
                $em->persist($panier);
                $em->flush();
                 
                return $this->redirectToRoute('list_panier');

            }


        return $this->render('panier.html.twig',array('form' => $form->createView()));
    }

}

/**
 * @Route("/list", name="list_panier")
 */
public function listAction(Request $request)
{
     //recuperer l'utilisateur
    $user = $this->container->get('security.token_storage')->getToken()->getUser();
    //récupérer son username
    $username = $user->getUsername();
    //récupérer les prodduits commandés de cet utilisateur
    $paniers=$this->getDoctrine()->getRepository('AppBundle:AddPan')->findBy(array('client' =>$username));
    return $this->render('listPan.html.twig', array('paniers'=>$paniers));
}

/**
 * @Route("/listCommande", name="list_commande")
 */
public function listcommandeAction(Request $request)
{
     //recuperer l'utilisateur
    $user = $this->container->get('security.token_storage')->getToken()->getUser();
    //récupérer son username
    $username = $user->getEmail();
    //récupérer les prodduits commandés de cet utilisateur
    $commandes=$this->getDoctrine()->getRepository('AppBundle:Commande')->findBy(array('client' =>$username));
    return $this->render('listCommande.html.twig', array('commandes'=>$commandes));
}
/**
 * @Route("/delete/{id}", name="supp_panier")
 */
public function deleteAction($id)
{
  $em=$this->getDoctrine()->getManager();
  $pan=$em->getRepository('AppBundle:AddPan')->find($id);
  $em->remove($pan);
  $em->flush();
  $this->addFlash('message','commande supprimée');
  return $this->redirectToRoute('list_panier');
}

/**
     * @Route("/commande/{id}", name="commander")
     */
    public function commandeAction($id, Request $request)
    {
        //recuperer l'utilisateur
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        //récupérer son username
        $email = $user->getEmail();
        $panier= $this->getDoctrine()->getRepository('AppBundle:AddPan')->find($id);
        //recupérer id de produit
        $prd= $this->getDoctrine()->getRepository('AppBundle:Produit')->find($panier->getProduit()->getId());
        //recupérer sa quantité
        $qtproduit=$prd->getQte();

        //récupérer la quantité demandé
        $qtdemande=$panier->getQt();

        //récupérer la date actuelle

        $now = new \DateTime('now');
        $total=$panier->getPrixTotal();
        $commande= new Commande();
        $commande->setClient($email);
        $form= $this->createFormBuilder($commande)
            ->add('client',TextType::class, array('label' => 'Email', 'required' => true,'attr' =>array('class' =>'form-control','value'=> $email)))
            ->add('pays',choiceType::class, array('required' => true,'choices' =>array('Tunisie' =>'Tunisie', 'Algérie' =>'Algérie', 'Maroc' =>'Maroc', 'Egypt' => 'Egypte'),'attr' =>array('class' =>'form-control')))
            ->add('adresse',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control')))

            ->add('ville',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control')))
            ->add('tel',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control')))
            ->add('total',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control', 'value'=> $total)))
            ->add('nomCarte',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control')))
            ->add('numeroCarte',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control', 'placeholder' => '....  ....  ....  ....', 'max_length' => 16)))
            ->add('codeCarte',TextType::class, array('required' => true,'attr' =>array('class' =>'form-control', 'placeholder' => 'CVC', 'max_length' => 4)))



            ->add('Save',SubmitType::class, array('label' =>'Valider','attr' =>array('class' =>'btn btn-primary')))


            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //get data
            $client = $form['client']->getData();

            $pays = $form['pays']->getData();
            $adresse = $form['adresse']->getData();


            $ville = $form['ville']->getData();
            $tel = $form['tel']->getData();
            $total = $form['total']->getData();
            $nomc = $form['nomCarte']->getData();
            $numeroc = $form['numeroCarte']->getData();
            $codecarte = $form['codeCarte']->getData();

            //set data
             $commande->setClient($client);
             $commande->getPays($pays);
             $commande->setAdresse($adresse);
             $commande->setVille($ville);
             $commande->setTel($tel);
             $commande->setTotal($total);
             $commande->setNomCarte($nomc);
             $commande->setNumeroCarte($numeroc);
             $commande->setCodeCarte($codecarte);
             $commande->setDateCommande($now);
            //persit
            $em= $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();

            //supprimer du panier le produit commandé aprés facturation
            $em->remove($panier);
            $em->flush();
            //mise à jour de la quantité
            $prd->setQte(($qtproduit-$qtdemande));
            $em->persist($prd);
            $em->flush();
            $this->addFlash(
                'message',
                'C\'est terminée'
            );
            return $this->redirectToRoute('list_panier');

        }

        return $this->render('commande.html.twig',array('form' => $form->createView()));// replace this example code with whatever you need


    }


}
