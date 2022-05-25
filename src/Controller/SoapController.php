<?php

namespace App\Controller;

use App\Soap\SoapOperations;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SoapController extends AbstractController
{
	/**
     * @Route("/soap", name="soap")
     */
	// si SoapOperations service (parties à décommenter également ci-dessous et dans services.yaml)
	//public function soapAction(SoapOperations $soapOperations)
    public function soapAction()
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        $options= array(
            'trace'=>1,
            'encoding'  => 'UTF-8',
            'uri' => 'http://127.0.0.1:8000/soap',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => true,
            'soap_version' => SOAP_1_1
        );

		$soapServer = new \SoapServer("../soap.wsdl",$options);
		$soapServer->setObject(new SoapOperations($this->getDoctrine()));
        // si SoapOperations service
        //$soapServer->setObject($soapOperations);
		$response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
	}
}
?>