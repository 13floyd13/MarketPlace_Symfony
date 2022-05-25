<?php


namespace App\Controller;

use App\Service\SoapOperations;
use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Laminas\Soap\AutoDiscover;

class SoapGenController extends AbstractController
{
    /**
     * @Route("/soapgen", name="soapgen")
     */
    public function soapGenAction()
    {
        // Génération automatique
        $autodiscover = new AutoDiscover();

        // Défaut RPC/encoded, à décommenter si document/literal souhaité mais problème à l'appel
        /*$autodiscover->setOperationBodyStyle([
            'use'       => 'literal',
            //'encodingStyle' => 'http://schemas.xmlsoap.org/soap/encoding/',
            'namespace' => 'http://localhost:8000/soap'
        ]);

        $autodiscover->setBindingStyle([
            'style'     => 'document',
            'transport' => 'http://schemas.xmlsoap.org/soap/http',
        ]);*/

        $autodiscover->setClass('\App\Soap\SoapOperations')
            ->setUri('http://localhost:8000/soap')
            ->setServiceName('SoapGenService');
        header('Content-Type: application/wsdl+xml');
        $autodiscover->generate();
        $autodiscover->dump("../soap.wsdl");
        return new Response($autodiscover->toXml());

        // exemple de génération de WSDL avec appel de méthodes successives ("à la main")
        /*$wsdl = new Wsdl("structuresWsdl",'http://localhost:8000/soap');
        $wsdl->addComplexType('\App\Soap\SecteurSoap');
        $wsdl->dump("../soap.wsdl");
        return new Response($wsdl->toXml());*/
    }
}