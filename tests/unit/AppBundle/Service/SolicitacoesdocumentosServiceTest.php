<?php

namespace AppBundle\Service;

use Nasajon\MDABundle\Entity\Meurh\Solicitacoesdocumentos;
use AppBundle\Service\Meurh\SolicitacoesdocumentosService;
use Nasajon\MDABundle\Repository\Meurh\SolicitacoesdocumentosRepository;
use Nasajon\MDABundle\Service\Meurh\SolicitacoesService;

class SolicitacoesdocumentosServiceTest extends \Codeception\Test\Unit
{

    protected $logged_user = ["nome" => "Teste", "email" => "teste@nasajon.com.br", "id" => "31c06307-4b04-45da-886a-608f00172a15"];
    protected $solicitacoesdocumentosRepository;
    protected $solicitacoesService;
    protected $adapter;
    protected $fixedAttributes;
    protected $solicitacoesdocumentosService;

    public function setUp() {
        $this->solicitacoesdocumentosRepository = $this->getMockBuilder(SolicitacoesdocumentosRepository::class)
            ->setMethods(['begin', 'commit', 'rollBack', 'getRepository', 'update', 'insert', 'delete', 'prepare', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->solicitacoesService = $this->getMockBuilder(SolicitacoesService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fixedAttributes = \Codeception\Util\Stub::makeEmpty(\Symfony\Component\DependencyInjection\ParameterBag\ParameterBag::class);

        $this->adapter = \Codeception\Util\Stub::makeEmpty(\Gaufrette\Adapter::class);

        $this->solicitacoesdocumentosService = $this->getMockBuilder(SolicitacoesdocumentosService::class)
        ->disableOriginalConstructor()
        ->getMock();
    }

    public function testInsereSolicitacaoDocumentoComSucessoQuandoSolicitacaoTemSicutacaoIgualAZero() {
        $solicitacao = [
            "solicitacao" => '3bda2ecb-2170-4b75-b072-ff3c51de4503',
            "situacao" => 0,
            "tenant" => 47
        ];

        $entity = new Solicitacoesdocumentos();

        $entity->setSolicitacao("3bda2ecb-2170-4b75-b072-ff3c51de4503");
        $entity->setConteudo("file");
        $entity->setTenant(47);

        $this->solicitacoesService
            ->expects($this->any())
            ->method('find')
            ->with($solicitacao["solicitacao"], $solicitacao["tenant"])
            ->willReturn($solicitacao);

        $this->solicitacoesdocumentosService
            ->expects($this->any())
            ->method('createFile')
            ->with("enderecodoarquivo", $entity->getConteudo())
            ->willReturn(true);

        $this->solicitacoesdocumentosRepository
            ->expects($this->any())
            ->method('insert')
            ->with($this->logged_user, $entity->getTenant(), $entity)
            ->willReturn($entity->getSolicitacaodocumento());

        $response = $this->solicitacoesdocumentosService->insert($this->logged_user, $entity->getTenant(), $entity);
        $this->assertEquals($entity->getSolicitacaodocumento(), $response);
    }


    public function testInsereSolicitacaoDocumentoComFalhaQuandoSolicitacaoTemSituacaoDiferenteDeZero() {
        $solicitacao = [
            "solicitacao" => '3bda2ecb-2170-4b75-b072-ff3c51de4503',
            "situacao" => 1,
            "tenant" => 47
        ];

        $entity = new Solicitacoesdocumentos();

        $entity->setSolicitacao("3bda2ecb-2170-4b75-b072-ff3c51de4503");
        $entity->setConteudo("file");
        $entity->setTenant(47);

        $this->solicitacoesService
            ->expects($this->any())
            ->method('find')
            ->with($solicitacao["solicitacao"], $solicitacao["tenant"])
            ->willReturn($solicitacao);

        $solicitacoesdocumentosService = new SolicitacoesdocumentosService($this->solicitacoesdocumentosRepository, $this->adapter, $this->solicitacoesService, $this->fixedAttributes);
        $this->setExpectedException("Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException");
        $solicitacoesdocumentosService->insert(["nome"=> "teste"], $entity->getTenant(), $entity);
    }

    public function testDeletaSolicitacaoDocumentoComSucessoQuandoSolicitacaoTemSicutacaoIgualAZero() {
        $solicitacao = [
            "solicitacao" => '3bda2ecb-2170-4b75-b072-ff3c51de4503',
            "situacao" => 0,
            "tenant" => 47
        ];

        $entity = new Solicitacoesdocumentos();

        $entity->setSolicitacao("3bda2ecb-2170-4b75-b072-ff3c51de4503");
        $entity->setConteudo("file");
        $entity->setTenant(47);

        $this->solicitacoesService
            ->expects($this->any())
            ->method('find')
            ->with($solicitacao["solicitacao"], $solicitacao["tenant"])
            ->willReturn($solicitacao);

        $this->solicitacoesdocumentosService
            ->expects($this->any())
            ->method('deleteFile')
            ->with("enderecodoarquivo")
            ->willReturn(true);

        $this->solicitacoesdocumentosRepository
            ->expects($this->any())
            ->method('delete')
            ->with($entity->getTenant(), $entity)
            ->willReturn($entity->getSolicitacaodocumento());

        $response = $this->solicitacoesdocumentosService->delete($entity->getTenant(), $entity);
        $this->assertEquals($entity->getSolicitacaodocumento(), $response);
    }


    public function testDeletaSolicitacaoDocumentoComFalhaQuandoSolicitacaoTemSituacaoDiferenteDeZero() {
        $solicitacao = [
            "solicitacao" => '3bda2ecb-2170-4b75-b072-ff3c51de4503',
            "situacao" => 1,
            "tenant" => 47
        ];

        $entity = new Solicitacoesdocumentos();

        $entity->setSolicitacao("3bda2ecb-2170-4b75-b072-ff3c51de4503");
        $entity->setConteudo("file");
        $entity->setTenant(47);

        $this->solicitacoesService
            ->expects($this->any())
            ->method('find')
            ->with($solicitacao["solicitacao"], $solicitacao["tenant"])
            ->willReturn($solicitacao);

        $solicitacoesdocumentosService = new SolicitacoesdocumentosService($this->solicitacoesdocumentosRepository, $this->adapter, $this->solicitacoesService, $this->fixedAttributes);
        $this->setExpectedException("Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException");
        $solicitacoesdocumentosService->delete($entity->getTenant(), $entity);
    }
}
