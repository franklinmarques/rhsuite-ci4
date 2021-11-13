<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico'); ?>">

    <title>RhSuite - Automação de RH</title>

    <link href="<?= base_url('compiled.css'); ?>" rel="stylesheet">

    <?php if (!empty($imagem_fundo)): ?>
        <style>
            .login-page {
                background-image: url(<?= '../imagens/usuarios/' . $imagem_fundo ?>);
            }
        </style>
    <?php endif; ?>

    <style>
        .background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1000;
            background: url(<?= '../imagens/usuarios/' . $imagem_fundo ?>) no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body class="login-page">

<video autoplay loop poster="<?= site_url('imagens/usuarios/' . $imagem_fundo) ?>" class="background-video">
    <source src="<?= site_url('videos/usuarios/' . $video_fundo) ?>" type="video/mp4">
</video>

<div id="cookie" class="text-danger text-center" style="background-color: #ffe; display: none;">
    Este site usa cookies! Habilite o uso de cookies em seu navegador para o correto funcionamento do site.
</div>

<div class="container">
    <?php
    if ($logoempresa) {
        $logo = site_url('imagens/usuarios/' . $logo);
        $hr = '<hr style="margin-top:10px; margin-bottom:10px;"/>';
    } else {
        $logo = site_url('assets/img/logorhsuite3.png');
        $cabecalho = '';
        $hr = '';
    }
    ?>
    <div style="width: 100%; max-width: 400px; margin: 0 auto;">
        <div align="center">
            <img src="<?= $logo ?>" style="width: auto; max-height: 100px; margin-bottom: 3%;">
            <h4 style="color: #111343; text-shadow: 1px 1px 1px rgba(255,255,255,0.5);">
                <strong><?= $cabecalho ?></strong></h4>
        </div>
    </div>
    <div class="login-wrapper">
        <!-- BEGIN alert -->
        <div id="alert" style="margin: 10px auto;"></div>
        <!-- END alert -->
        <!-- BEGIN Login Form -->
        <?= form_open('login/autenticar', 'data-aviso="alert" id="form-login" class="ajax-simple" autocomplete="off"') ?>
        <input type="hidden" name="geolocalizacao" value="">

        <div class="card">
            <div class="card-header">
                <h4> Entre na sua conta</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text" id="user_addon"><i class="fa fa-user"></i></span>
                        <input type="text" name="email" placeholder="E-mail" aria-label="E-mail" class="form-control"
                               aria-describedby="user_addon" autofocus="">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text" id="senha_addon"><i class="fa fa-lock"></i></span>
                        <input type="password" name="senha" placeholder="Senha" aria-label="Senha"
                               class="form-control" aria-describedby="senha_addon" autocomplete="new-password">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" id="btnLogin" class="btn btn-primary">Entrar</button>
                </div>
                <!-- END Login Form -->
                <hr style="margin-top: 10px; margin-bottom: 10px;"/>
                <p class="clearfix">
                    <a href="#" class="goto-forgot pull-left" style="color: #111343;">Esqueceu a senha?</a>
                </p>
            </div>
        </div>
        <?= form_close() ?>

        <!-- BEGIN Forgot Password Form -->
        <?= form_open('login/recuperar_senha', 'data-aviso="alert" id="form-forgot" class="ajax-simple" style="display:none"') ?>
        <div class="card">
            <div class="card-header">
                <h4>Recupere sua senha1</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text" id="email_addon"><i class="fa fa-envelope"></i></span>
                        <input type="text" name="email" placeholder="E-mail" aria-label="E-mail" class="form-control"
                               aria-describedby="email_addon">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="controls">
                        <button type="submit" class="btn btn-primary form-control">Recuperar</button>
                    </div>
                </div>
                <hr>
                <p class="clearfix">
                    <a href="#" class="goto-login pull-left" style="color: #111343;">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                </p>
            </div>
        </div>
        <?= form_close() ?>

        <div class="d-grid gap-2">
            <a href="<?= base_url('vagas') ?>" class="btn btn-primary"
               style="box-shadow: 1px 2px 4px rgba(0, 0, 0, .15);">
                Consultar vagas | Cadastrar currículo
            </a>
        </div>
        <br>
        <?php if ($visualizacao_pilula_conhecimento): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_pilulas_conhecimento"
                    style="margin-top: 3px; box-shadow: 1px 2px 4px rgba(0, 0, 0, .15);">
                Pílulas de Conhecimento
            </button>
        <?php endif; ?>

    </div>

    <div id="modal_pilulas_conhecimento" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" style="float:right;" class="btn btn-default" data-dismiss="modal">Fechar
                    </button>
                    <h4 class="modal-title text-primary">
                        <strong>Programa de Formação Continuada - Pílulas de Conhecimento</strong>
                    </h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="area_conhecimento" class="col-sm-3 text-primary control-label"><strong>Área
                                    de conhecimento</strong></label>
                            <div class="col-sm-4">
                                <?= form_dropdown('area_conhecimento', $area_conhecimento, '', 'id="area_conhecimento" class="form-control" autocomplete="off"') ?>
                            </div>
                            <label for="tema" class="col-sm-1 text-primary control-label"><strong>Tema</strong></label>
                            <div class="col-sm-4">
                                <?= form_dropdown('tema', $tema, '', 'id="tema" class="form-control" autocomplete="off"') ?>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-xs-12">
                                <div id="conteudo" class="embed-responsive embed-responsive-16by9"></div>
                            </div>
                        </div>
                    </form>
                    <div class="form-group">
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>
<footer class="footer">
    <p style="text-align: center; color: rgb(21,24,96); text-shadow: -1px -1px 0 rgba(255,255,255,0.51);">Copyright
        &copy;
        <?= date('Y') . ' ' . $nome . ' - ' . (strlen($cabecalho) > 0 ? $cabecalho : 'Automação de RH') ?><br>
        <a href="mailto:<?= $email ?>" style="color: #151860;"><?= $email ?></a>
        <!--| <a
                href="mailto:contato@multirh.com.br" style="color: #151860;">contato@multirh.com.br</a>-->
    </p>
</footer>

<!-- Placed js at the end of the document so the pages load faster -->

<!--Core js-->
<script src="<?= base_url('compiled.js') ?>"></script>

<script>
    if (navigator.cookieEnabled) {
        $('#cookie').hide();
    } else {
        $('#cookie').show();
    }

    function goToForm(form) {
        $('#alert').slideUp(400, function () {
            $('#alert').html('').hide();
            $('.login-wrapper > form:visible').fadeOut(500, function () {
                $('#form-' + form).fadeIn(500);
            });
        });
    }

    $(function () {
        $('.goto-login').click(function () {
            goToForm('login');
        });
        $('.goto-forgot').click(function () {
            goToForm('forgot');
        });
        $('.goto-register').click(function () {
            goToForm('register');
        });
    });

    $(document).ajaxComplete(function (event, jqXHR) {
        let retorno = jqXHR.responseJSON.retorno;
        if (retorno !== undefined && retorno === 1) {
            $('#form-login [name="possui_apontamento_horas"]').prop('checked', false);
        }
    });


    $('#area_conhecimento').on('change', function () {
        let tema = $('#tema').val();
        $.ajax({
            'url': "<?= site_url('login/filtrar_temas') ?>",
            'type': 'POST',
            'dataType': 'json',
            'data': {
                'area_conhecimento': this.value,
                'tema': tema
            },
            'success': function (json) {
                if (json.erro) {
                    alert(json.erro);
                } else {
                    $('#tema').html($(json.tema).html());

                    if ($('#tema').val() !== tema) {
                        $('#tema').trigger('change');
                    }
                }
            },
            'error': function (jqXHR, textStatus, errorThrown) {
                alert('Erro ao montar a estrutura');
            }
        });
    });


    $('#tema').on('change', function () {
        $.ajax({
            'url': "<?= site_url('login/mostrar_pilula_conhecimento') ?>",
            'type': 'POST',
            'dataType': 'json',
            'data': {
                'tema': this.value
            },
            'success': function (json) {
                if (json.erro) {
                    alert(json.erro);
                } else if (json.conteudo) {
                    $('#conteudo').html($(json.conteudo).html());
                } else {
                    $('#conteudo').html('');
                }
            },
            'error': function (jqXHR, textStatus, errorThrown) {
                alert('Erro ao montar a estrutura');
            }
        });
    });

    function obter_geolocalizacao() {
        if ($('#form-login [name="possui_apontamento_horas"]').is(':checked') === false) {
            $('#form-login [name="geolocalizacao"]').val('');
            return false;
        }
        $('#btnLogin').prop('disabled', true);
        if (navigator.geolocation) {
            showPosition = function (position) {
                let value = position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4);
                $('#form-login [name="geolocalizacao"]').val(value);
                $('#btnLogin').prop('disabled', false);
            }
            showError = function (error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        // alert('Permissão negada.');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        // alert('Posição indisponível.');
                        break;
                    case error.TIMEOUT:
                        // alert('A requisição expirou.');
                        break;
                    case error.UNKNOWN_ERROR:
                        // alert('Ocorreu um erro desconhecido.');
                        break;
                }
                $('#btnLogin').prop('disabled', false);
            }
            navigator.geolocation.getCurrentPosition(showPosition, showError, {'enableHighAccuracy': true});
        } else {
            // alert('A geolocalização não é suportada pelo navegador.');
            $('#btnLogin').prop('disabled', false);
        }
    }

</script>

</body>
</html>
