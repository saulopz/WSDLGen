<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'Tool.php';
include_once 'Operation.php';

class WSDLGen
{
    private $uri;
    private $local;
    private $service;
    private $server;
    private $wsName;
    private $wsdl;
    private $operations;

    public function __construct()
    {
        global $URI, $FILE_NAME, $SERVICE_NAME, $LOCAL;
        $this->local = $LOCAL;
        $this->service = $FILE_NAME;
        $this->wsName = $SERVICE_NAME;
        $this->wsdl = "";
        $this->uri = $URI;
    }

    function operation($operation, $parmIn, $parmOut, $encodingStyle, $description)
    {
        $this->operations[] = new Operation($operation, $parmIn, $parmOut, $encodingStyle, $description);
    }

    function dump()
    {
        // if (count($this->operations) == 0) return;
        $fname = "wsdl/" . $this->wsName . ".wsdl";
        if (!file_exists($fname)) {
            $this->generate();
            $handle = fopen($fname, "w");
            fwrite($handle, $this->wsdl);
            fclose($handle);
        }
        $this->server = new SoapServer($fname, array(
            'uri' => $this->uri
        ));
        if (count($this->operations) != 0) {
            reset($this->operations);
            foreach($this->operations as $name => $value) {
                $this->server->addFunction($value->name);
            }
        }
        $this->server->handle();
    }

    private function generate()
    {
        $wsdl = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $wsdl .= "<wsdl:definitions\n";
        $wsdl .= "\ttargetNamespace=\"" . $this->uri . "\"\n";
        $wsdl .= "\txmlns:apachesoap=\"http://xml.apache.org/xml-soap\"\n";
        $wsdl .= "\txmlns:impl=\"" . $this->uri . "\"\n";
        $wsdl .= "\txmlns:intf=\"" . $this->uri . "\"\n";
        $wsdl .= "\txmlns:soapenc=\"http://schemas.xmlsoap.org/soap/encoding/\"\n";
        $wsdl .= "\txmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\"\n";
        $wsdl .= "\txmlns:wsdlsoap=\"http://schemas.xmlsoap.org/wsdl/soap/\"\n";
        $wsdl .= "\txmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">\n\n";

        // response
        for ($i = 0; $i < count($this->operations); $i++) {
            $wsdl .= "\t<wsdl:message name=\"" . $this->operations[$i]->name . "Response\">\n";
            reset($this->operations[$i]->parmOut);
            foreach($this->operations[$i]->parmOut as $name => $value) {
                $wsdl .= "\t\t<wsdl:part name=\"" . $name . "\" type=\"" . $value . "\" />\n";
            }
            $wsdl .= "\t</wsdl:message>\n\n";
        }

        // request
        for ($i = 0; $i < count($this->operations); $i++) {
            $wsdl .= "\t<wsdl:message name=\"" . $this->operations[$i]->name . "Request\">\n";
            reset($this->operations[$i]->parmIn);
            foreach ($this->operations[$i]->parmIn as $name => $value) {
                $wsdl .= "\t\t<wsdl:part name=\"" . $name . "\" type=\"" . $value . "\" />\n";
            }
            $wsdl .= "\t</wsdl:message>\n\n";
        }

        // portType
        $wsdl .= "\t<wsdl:portType name=\"" . $this->wsName . "\">\n";
        foreach($this->operations as $name => $value) {
            $wsdl .= "\t\t<wsdl:operation name=\"" . $value->name . "\" parameterOrder=\"";
            $first = true;
            reset($value->parmIn);
            foreach ($value->parmIn as $n => $v) {
                if ($first) {
                    $wsdl .= $n;
                    $first = false;
                } else {
                    $wsdl .= " " . $n;
                }
            }
            $wsdl .= "\">\n";
            // $wsdl .= "\t\t\t<documentation>".$value->description."</documentation>\n";
            $wsdl .= "\t\t\t<wsdl:input message=\"impl:" . $value->name . "Request\" name=\"" . $value->name . "Request\" />\n";
            $wsdl .= "\t\t\t<wsdl:output message=\"impl:" . $value->name . "Response\" name=\"" . $value->name . "Response\" />\n";
            $wsdl .= "\t\t</wsdl:operation>\n\n";
        }
        $wsdl .= "\t</wsdl:portType>\n\n";

        $wsdl .= "\t<wsdl:binding name=\"" . $this->wsName . "SoapBinding\" type=\"impl:" . $this->wsName . "\">\n\n";
        $wsdl .= "\t\t<wsdlsoap:binding style=\"rpc\" transport=\"http://schemas.xmlsoap.org/soap/http\" />\n\n";

        reset($this->operations);
        foreach ($this->operations as $name => $value) {
            $wsdl .= "\t\t<wsdl:operation name=\"" . $value->name . "\">\n";
            $wsdl .= "\t\t\t<wsdlsoap:operation soapAction=\"urn:" . $this->wsName . "#" . $value->name . "\" />\n";
            $wsdl .= "\t\t\t<wsdl:input name=\"" . $value->name . "Request\">\n";
            $wsdl .= "\t\t\t\t<wsdlsoap:body encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\"\n";
            $wsdl .= "\t\t\t\t\tnamespace=\"http://DefaultNamespace\" use=\"" . $value->encodingStyle . "\" />\n";
            $wsdl .= "\t\t\t</wsdl:input>\n";

            $wsdl .= "\t\t\t<wsdl:output name=\"" . $value->name . "Response\">\n";
            $wsdl .= "\t\t\t\t<wsdlsoap:body encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\"\n";
            $wsdl .= "\t\t\t\t\tnamespace=\"" . $this->uri . "\" use=\"" . $value->encodingStyle . "\" />\n";
            $wsdl .= "\t\t\t</wsdl:output>\n";
            $wsdl .= "\t\t</wsdl:operation>\n\n";
        }
        $wsdl .= "\t</wsdl:binding>\n\n";

        $wsdl .= "\t<wsdl:service name=\"" . $this->wsName . "Service\">\n";
        $wsdl .= "\t\t<wsdl:port binding=\"impl:" . $this->wsName . "SoapBinding\" name=\"" . $this->wsName . "\">\n";
        $wsdl .= "\t\t\t<wsdlsoap:address location=\"" . $this->uri . "\" />\n";
        $wsdl .= "\t\t</wsdl:port>\n";
        $wsdl .= "\t</wsdl:service>\n";

        $wsdl .= "</wsdl:definitions>";
        $this->wsdl = $wsdl;
    }
}
