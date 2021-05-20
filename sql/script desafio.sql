
--   drop function desafio.transferencia(a_objeto desafio.t_transferencia);
  
  
--   drop type desafio.t_transferencia;
       
--   drop table desafio.transacoes;
      
--   drop table desafio.carteiras;
   
--   drop table desafio.usuarios;

  



--
---Criação da schema principal-
--
CREATE SCHEMA if not exists desafio;

CREATE OR REPLACE FUNCTION public.uuid_generate_v4()
 RETURNS uuid
 LANGUAGE c
 PARALLEL SAFE STRICT
AS '$libdir/uuid-ossp', $function$uuid_generate_v4$function$
;



--
---Criação da tabela de usuários-
--
create table desafio.usuarios(
    usuario uuid DEFAULT uuid_generate_v4(), 
   	nome character varying(50) not null,
    email character varying(30) not null,
    senha character varying(30) not null,
    cpfcnpj character varying(18) not null,
    tipousuario smallint,
    primary key(usuario)
);

--
---E-mail ou Cpf/Cnpj únicos
--
ALTER TABLE desafio.usuarios ADD CONSTRAINT "UK_desafio.email" UNIQUE(email);
ALTER TABLE desafio.usuarios ADD CONSTRAINT "UK_desafio.cpfcnpj" UNIQUE(cpfcnpj);

--
---Criação da tabela de carteiras-
--
create table desafio.carteiras(
    carteira uuid DEFAULT uuid_generate_v4(), 
    usuario uuid not null,
   	saldo bigint not null,
    primary key(carteira)
);


--
---Relacionamento da Carteira com o Usuário-
--
ALTER TABLE desafio.carteiras ADD CONSTRAINT "FK_desafio.carteiras_usuario" FOREIGN KEY (usuario)
    REFERENCES desafio.usuarios (usuario) MATCH simple;
   

--
---Criação da tabela de transacoes-
--
create table desafio.transacoes(
    transacao uuid DEFAULT uuid_generate_v4(), 
    origem uuid not null,
    destino uuid not null,
   	valor bigint not null,
   	data timestamp NOT NULL DEFAULT now(),
    primary key(transacao)
);
     
 --
---Relacionamento da Carteira com o Usuário-
--
ALTER TABLE desafio.transacoes ADD CONSTRAINT "FK_desafio.transacoes_origem" FOREIGN KEY (origem)
    REFERENCES desafio.carteiras (carteira) MATCH simple;

   
ALTER TABLE desafio.transacoes ADD CONSTRAINT "FK_desafio.transacoes_destino" FOREIGN KEY (destino)
    REFERENCES desafio.carteiras (carteira) MATCH simple;

   
 CREATE TYPE desafio.t_transferencia AS (
	  valor bigint,                  
	  origem uuid,
	  destino uuid
 );
                 

CREATE OR REPLACE FUNCTION  desafio.transferencia(a_objeto desafio.t_transferencia)
	    RETURNS uuid
	    LANGUAGE plpgsql
	AS $function$
	declare
	    _id uuid;
	begin
		
		update desafio.carteiras 
		set saldo = saldo - a_objeto.valor
		where carteira = a_objeto.origem;
	
		update desafio.carteiras 
		set saldo = saldo + a_objeto.valor
		where carteira = a_objeto.destino;
	
		insert into desafio.transacoes  
			(origem,
		    destino,
		   	valor,
		   	data) 
		values 
			(a_objeto.origem,
			a_objeto.destino,
			a_objeto.valor,
			now())
		returning transacao into _id;
	
	    RETURN _id;
	END;
	$function$;

 


 insert into desafio.usuarios 
 (usuario,nome,email,senha,cpfcnpj,tipousuario)
 values  
('9fc47cd6-533f-44a4-8baa-add479d06c26',	'Teste 1',	'teste1@teste.com',	'123456',	'370.905.280-73',	0),
('a04627f4-ef84-45a8-bca7-e0e11bf0ba71',	'Teste 2',	'teste2@teste.com',	'123456',	'181.560.890-06',	0),
('95651bda-eb74-464e-9e3d-a4326589ccbc',	'Teste 3',	'teste3@teste.com',	'123456',	'89.244.647/0001-72',	1),
('10d6bacc-f78b-405b-be37-a021ee7a21a3',	'Teste 4',	'teste4@teste.com',	'123456',	'14.886.493/0001-13',	1);


 insert into desafio.carteiras 
 (carteira,usuario,saldo)
 values  
('9fc47cd6-533f-44a4-8baa-add479d06c30','9fc47cd6-533f-44a4-8baa-add479d06c26', 1000),
('9fc47cd6-533f-44a4-8baa-add479d06c27','a04627f4-ef84-45a8-bca7-e0e11bf0ba71', 2000),
('9fc47cd6-533f-44a4-8baa-add479d06c28','95651bda-eb74-464e-9e3d-a4326589ccbc', 3000),
('9fc47cd6-533f-44a4-8baa-add479d06c29','10d6bacc-f78b-405b-be37-a021ee7a21a3', 4000);


select * from desafio.usuarios;

select * from desafio.carteiras;


select * from desafio.transacoes;


SELECT *
    FROM desafio.transferencia(row(
            1500,                  
            '9fc47cd6-533f-44a4-8baa-add479d06c28'::uuid,
            '9fc47cd6-533f-44a4-8baa-add479d06c27'::uuid
        )::desafio.t_transferencia
);

