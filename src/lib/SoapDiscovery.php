<?php

/**
 * Class SoapDiscovery
 */
class SoapDiscovery
{

    private $class_name = '';
    private $service_name = '';

    /**
     * SoapDiscovery::__construct() SoapDiscovery class Constructor.
     *
     * @param string $class_name
     * @param string $service_name
     * */
    public function __construct ($class_name = '', $service_name = '')
    {
        $this->class_name = $class_name;
        $this->service_name = $service_name;
    }

    /**
     * SoapDiscovery::getWSDL() Returns the WSDL of a class if the class is instantiable.
     *
     * @return string
     * */
    public function getWSDL ()
    {
        if (empty($this->service_name)) {
            throw new Exception('No service name.');
        }
        $headerWSDL = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $headerWSDL .= "<definitions xmlns=\"http://schemas.xmlsoap.org/wsdl/\" xmlns:tns=\"urn:" . $this->service_name . "wsdl\" xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\" xmlns:soap-enc=\"http://schemas.xmlsoap.org/soap/encoding/\" name=\"$this->service_name\" targetNamespace=\"urn:" . $this->service_name . "wsdl\">\n";

        if (empty($this->class_name)) {
            throw new Exception('No class name.');
        }

        $class = new ReflectionClass($this->class_name);

        if (!$class->isInstantiable()) {
            throw new Exception('Class is not instantiable.');
        }

        $methods = $class->getMethods();

        $portTypeWSDL = "\t<wsdl:portType name=\"" . $this->service_name . "PortType\">\n";
        $bindingWSDL = "\t<wsdl:binding name=\"" . $this->service_name . 'Binding" type="tns:' . $this->service_name . "PortType\">\n\t\t<soap:binding style=\"rpc\" transport=\"http://schemas.xmlsoap.org/soap/http\" />\n";
        $serviceWSDL = "\t<wsdl:service name=\"" . $this->service_name . "Service\">\n\t\t<wsdl:port name=\"" . $this->service_name . 'Port" binding="tns:' . $this->service_name . "Binding\">\n\t\t\t<soap:address location=\"http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . "\" />\n\t\t</wsdl:port>\n\t</wsdl:service>\n";
        $messageWSDL = '';
        foreach ($methods as $method) {
            if ($method->isPublic() && !$method->isConstructor()) {
                $portTypeWSDL .= "\t\t<wsdl:operation name=\"" . $method->getName() . "\">\n" . "\t\t\t<wsdl:documentation/>\n" . "\t\t\t<wsdl:input message=\"tns:" . $method->getName() . "In\" />\n\t\t\t<wsdl:output message=\"tns:" . $method->getName() . "Out\" />\n\t\t</wsdl:operation>\n";

                $bindingWSDL .= "\t\t<wsdl:operation name=\"" . $method->getName() . "\">\n";
                $bindingWSDL .= "\t\t\t<soap:operation soapAction=\"urn:" . $this->service_name . 'wsdl#' . $this->class_name . '#' . $method->getName() . "\" style=\"rpc\" />\n";
                $bindingWSDL .= "\t\t\t<wsdl:input>\n";
                $bindingWSDL .= "\t\t\t\t<soap:body use=\"encoded\" namespace=\"urn:" . $this->service_name . "wsdl\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n";
                $bindingWSDL .= "\t\t\t</wsdl:input>\n";
                $bindingWSDL .= "\t\t\t<wsdl:output>\n";
                $bindingWSDL .= "\t\t\t\t<soap:body use=\"encoded\" namespace=\"urn:" . $this->service_name . "wsdl\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n";
                $bindingWSDL .= "\t\t\t</wsdl:output>\n";
                $bindingWSDL .= "\t\t</wsdl:operation>\n";

                $messageWSDL .= "\t<wsdl:message name=\"" . $method->getName() . "In\">\n";
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    $messageWSDL .= "\t\t<wsdl:part name=\"" . $parameter->getName() . "\" type=\"xsd:string\" />\n";
                }
                $messageWSDL .= "\t</wsdl:message>\n";
                $messageWSDL .= "\t<wsdl:message name=\"" . $method->getName() . "Out\">\n";
                $messageWSDL .= "\t\t<wsdl:part name=\"return\" type=\"xsd:string\" />\n";
                $messageWSDL .= "\t</wsdl:message>\n";
            }
        }
        $portTypeWSDL .= "\t</wsdl:portType>\n";
        $bindingWSDL .= "\t</wsdl:binding>\n";
        return sprintf('%s%s%s%s%s%s', $headerWSDL, $messageWSDL, $portTypeWSDL, $bindingWSDL, $serviceWSDL, '</definitions>');
    }

}