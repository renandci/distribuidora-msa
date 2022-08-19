<style>
  .container-fluid-a {
    position: absolute;
    left: 800px;
    top: 200px;
    width: 500px;
    color: #fff
  }
  .container-fluid-a b {
    font-size: 35px;
  }

  @media (max-width: 767px) {
    .container-fluid-a b {
      font-size: 12px;
    }

    .container-fluid-a {
      left: auto;
      right: 0px;
      top: 0px;
      width: 178px;
      font-size: 12px;
    }
  }

  .container-fluid-b {
    font-weight: bold;
    font-size: 24px;
    position: absolute;
    left: 150px;
    top: 200px;
    width: 500px;
  }

  @media (max-width: 767px) {
    .container-fluid-b {
      width: 215px;
      font-size: 12px;
      left: 0;
      top: 18px;
    }
  }
</style>

<div style="background-color: #f5f5f5;" class="banner">
  <div class="banner-index">
    <div class="clearfix">
      <img src="/public/imagens/b1.jpg" class="lazyOwl" width="100%" />
      <div class="container-fluid container-fluid-a">
        <b>Somos especializados em qualidade</b>
        <br>
        <br>
        Há mais de 10 anos nossas ações são voltadas para a satisfação dos nossos clientes, através da excelência no atendimento e prestação de serviços.
      </div>
      <a href="/produtos" class="btn btn-banner-1">Saiba Mais</a>
    </div>
  </div>
</div>

<div style="background-color: #f5f5f5;" class="banner">
  <div class="banner-index">
    <div class="clearfix">
      <img id="banner2" src="public/imagens/b2.jpg" class="lazyOwl" width="100%" />
      <div class="container-fluid container-fluid-b">
        Somos comprometidos em oferecer soluções completas para o canal food service e dispomos de um portfólio completo para atender as necessidades dos segmentos de: <b style="color: #193CA2">sorveterias,confeitarias e embalagens.</b>
      </div>
      <a href="/produtos" class="btn btn-banner-2">Conheça Nossos Produtos</a>
    </div>
  </div>
</div>

<style>
  /* .btn-banner-2 {
    position: absolute;
    font-size: 30px;
    left: 150px;
    top: 450px;
    width: 500px;
    border-radius: 27px;
    background-color: #fbe992 !important;
  } */
</style>
