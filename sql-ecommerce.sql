-- CRIAR O SCHEMA e2 NO BANCO PARA RODAR OS SCRIPTS
-- SCRIPTS PARA O POSTGRESQL
-- DESENVOLVIDO POR LUCAS HAYASHI, COM O APOIO DO GRUPO

CREATE SCHEMA e2

CREATE SEQUENCE e2.seq_usuario;
  
CREATE TABLE e2.cadastros
(
    idusuario    INT NOT NULL,
    usuario      VARCHAR (25) NOT NULL,
    senha        VARCHAR (32) NOT NULL,
    nome		      VARCHAR (25) NOT NULL,
    sobrenome    VARCHAR(50),
    cpf          VARCHAR(14) NOT NULL,
    celular      VARCHAR (15),
    logradouro   VARCHAR(50),
    bairro       VARCHAR(20),
    cidade       VARCHAR(20),
    estado       VARCHAR(2),
    cep          VARCHAR(9),
    adminuser    VARCHAR(3),
    excluido     VARCHAR(3),
    dt_exclusao  DATE,
    CONSTRAINT pk_idusuario PRIMARY KEY (idusuario),
    CONSTRAINT uk_usuario UNIQUE (usuario, cpf)
);

--ADICIONA USUÁRIO PARA TESTE
INSERT INTO e2.cadastros (idusuario, usuario, senha, nome, sobrenome, cpf, celular, logradouro, bairro, cidade, estado, cep, adminuser, excluido) VALUES(NEXTVAL('e2.seq_usuario'), 'admin',md5('admin'),'Administrador', '', '45439866876', '14988003272','Rua Teste','Bairro Teste','Bauru','SP', '17032-520','Sim', 'Não');

INSERT INTO e2.cadastros (idusuario, usuario, senha, nome, sobrenome, cpf, celular, logradouro, bairro, cidade, estado, cep, adminuser, excluido) VALUES(NEXTVAL('e2.seq_usuario'), 'usuario',MD5('usuario'),'Usuário', '', '45439866876', '14988003272','Rua Teste','Bairro Teste','Bauru','SP', '17032-520','Não', 'Não');
  
  -----------------------------------------------------------

  CREATE TABLE e2.tamanho(
    idtamanho INT NOT NULL,
    nome VARCHAR(15),
    CONSTRAINT pd_idtamanho PRIMARY KEY (idtamanho)
  );
	
  INSERT INTO e2.tamanho (idtamanho, nome) VALUES ('1','Médio');
  INSERT INTO e2.tamanho (idtamanho, nome) VALUES ('2','Grande');
  INSERT INTO e2.tamanho (idtamanho, nome) VALUES ('3','Pequeno');


 -----------------------------------------------------------

CREATE SEQUENCE e2.seq_produto START 1000; 
 
CREATE TABLE e2.produto(
    idproduto 	 INT NOT NULL,
    nome 		 VARCHAR(200) NOT NULL,
    SKU			 VARCHAR(20),
    preco_custo  DECIMAL(10,2) NOT NULL,
    icms		 INT NOT NULL,
    margem_lucro INT NOT NULL,
    preco_venda  DECIMAL(10,2) NOT NULL,
    quantidade 	 INT NOT NULL,
    dt_cadastro  DATE,
    idtamanho    INT NOT NULL,
    disponivel   VARCHAR(3),
    imgname 	 VARCHAR(2048),
    CONSTRAINT fk_idtamanho FOREIGN KEY (idtamanho) REFERENCES e2.tamanho(idtamanho),
    CONSTRAINT pk_idproduto PRIMARY KEY (idproduto),
    CONSTRAINT uk_nome	UNIQUE (nome)
);


    -----------------------------------------------------------
	
CREATE table e2.vendas(
    idvenda BIGINT NOT NULL,
    idusuario INT NOT NULL,
    total VARCHAR(10),
    formaDePagamento VARCHAR(20),
    dataConfirmacao DATE,
    CONSTRAINT pk_idvenda PRIMARY KEY (idvenda),
    CONSTRAINT fk_idusuario FOREIGN KEY (idusuario) REFERENCES e2.cadastros(idusuario)  
);

	-----------------------------------------------------------
	
CREATE TABLE e2.carrinho (
    idusuario INT NOT NULL,
    idproduto INT NOT NULL,
    idvenda   BIGINT, 
    qtdcompra INT,
    concluido VARCHAR(3),
    CONSTRAINT fk_idusuario FOREIGN KEY (idusuario) REFERENCES e2.cadastros(idusuario),
    CONSTRAINT fk_idproduto FOREIGN KEY (idproduto) REFERENCES e2.produto(idproduto),
    CONSTRAINT fk_idvenda   FOREIGN KEY (idvenda)   REFERENCES e2.vendas(idvenda)
);

