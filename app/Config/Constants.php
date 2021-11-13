<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2592000);
defined('YEAR')   || define('YEAR', 31536000);
defined('DECADE') || define('DECADE', 315360000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


/*
|--------------------------------------------------------------------------
| Status de usuário
|--------------------------------------------------------------------------
|
| Índices de status utilizados na aplicação
|
*/
defined('ESTADO_DEFAULT') || define('ESTADO_DEFAULT', 35); // Estado de São Paulo
defined('CIDADE_DEFAULT') || define('CIDADE_DEFAULT', 3550308); // Cidade de São Paulo

/*
|--------------------------------------------------------------------------
| Status de usuário
|--------------------------------------------------------------------------
|
| Índices de status utilizados na aplicação
|
*/
defined('USUARIO_ATIVO')                    || define('USUARIO_ATIVO', 1);
defined('USUARIO_INATIVO')                  || define('USUARIO_INATIVO', 2);
defined('USUARIO_EM_EXPERIENCIA')           || define('USUARIO_EM_EXPERIENCIA', 3);
defined('USUARIO_EM_DESLIGAMENTO')          || define('USUARIO_EM_DESLIGAMENTO', 4);
defined('USUARIO_DESLIGADO')                || define('USUARIO_DESLIGADO', 5);
defined('USUARIO_AFASTADO_MATERNIDADE')     || define('USUARIO_AFASTADO_MATERNIDADE', 6);
defined('USUARIO_AFASTADO_APOSENTADORIA')   || define('USUARIO_AFASTADO_APOSENTADORIA', 7);
defined('USUARIO_AFASTADO_INSS')            || define('USUARIO_AFASTADO_INSS', 8);
defined('USUARIO_AFASTADO_ACIDENTE')        || define('USUARIO_AFASTADO_ACIDENTE', 9);
defined('USUARIO_DESISTIU_VAGA')            || define('USUARIO_DESISTIU_VAGA', 10);
defined('USUARIO_AFASTADO_ATESTADO')        || define('USUARIO_AFASTADO_ATESTADO', 11);
defined('USUARIO_ATIVAR')                   || define('USUARIO_ATIVAR', 12);
defined('USUARIO_DISTRATO_TEMPORARIO')      || define('USUARIO_DISTRATO_TEMPORARIO', 13);
defined('USUARIO_DISTRATO')                 || define('USUARIO_DISTRATO', 14);

/*
|--------------------------------------------------------------------------
| Níveis de acesso de usuário
|--------------------------------------------------------------------------
|
| Índices de níveis de acesso utilizados na aplicação
|
*/
defined('NIVEL_ACESSO_ADMINISTRADOR')           || define('NIVEL_ACESSO_ADMINISTRADOR', 1);
defined('NIVEL_ACESSO_MULTIPLICADOR')           || define('NIVEL_ACESSO_MULTIPLICADOR', 2);
defined('NIVEL_ACESSO_GESTOR')                  || define('NIVEL_ACESSO_GESTOR', 3);
defined('NIVEL_ACESSO_COLABORADOR_CLT')         || define('NIVEL_ACESSO_COLABORADOR_CLT', 4);
defined('NIVEL_ACESSO_CLIENTE_NIVEL_1')         || define('NIVEL_ACESSO_CLIENTE_NIVEL_1', 5);
defined('NIVEL_ACESSO_SELECIONADOR')            || define('NIVEL_ACESSO_SELECIONADOR', 6);
defined('NIVEL_ACESSO_PRESIDENTE')              || define('NIVEL_ACESSO_PRESIDENTE', 7);
defined('NIVEL_ACESSO_GERENTE')                 || define('NIVEL_ACESSO_GERENTE', 8);
defined('NIVEL_ACESSO_COORDENADOR')             || define('NIVEL_ACESSO_COORDENADOR', 9);
defined('NIVEL_ACESSO_SUPERVISOR')              || define('NIVEL_ACESSO_SUPERVISOR', 10);
defined('NIVEL_ACESSO_ENCARREGADO')             || define('NIVEL_ACESSO_ENCARREGADO', 11);
defined('NIVEL_ACESSO_LIDER')                   || define('NIVEL_ACESSO_LIDER', 12);
defined('NIVEL_ACESSO_CUIDADOR_COMUNITARIO')    || define('NIVEL_ACESSO_CUIDADOR_COMUNITARIO', 13);
defined('NIVEL_ACESSO_COLABORADOR_PJ')          || define('NIVEL_ACESSO_COLABORADOR_PJ', 14);
defined('NIVEL_ACESSO_REPRESENTANTE')           || define('NIVEL_ACESSO_REPRESENTANTE', 15);
defined('NIVEL_ACESSO_COLABORADOR_MEI')         || define('NIVEL_ACESSO_COLABORADOR_MEI', 16);
defined('NIVEL_ACESSO_VISTORIADOR')             || define('NIVEL_ACESSO_VISTORIADOR', 17);
defined('NIVEL_ACESSO_DIRETOR')                 || define('NIVEL_ACESSO_DIRETOR', 18);
defined('NIVEL_ACESSO_SUPERVISOR_REQUISITANTE') || define('NIVEL_ACESSO_SUPERVISOR_REQUISITANTE', 19);
defined('NIVEL_ACESSO_COLABORADOR_ME')          || define('NIVEL_ACESSO_COLABORADOR_ME', 20);
defined('NIVEL_ACESSO_COLABORADOR_LTDA')        || define('NIVEL_ACESSO_COLABORADOR_LTDA', 21);
defined('NIVEL_ACESSO_AUTONOMO')                || define('NIVEL_ACESSO_AUTONOMO', 22);
defined('NIVEL_ACESSO_FORNECEDOR')              || define('NIVEL_ACESSO_FORNECEDOR', 23);
defined('NIVEL_ACESSO_CLIENTE_NIVEL_2')         || define('NIVEL_ACESSO_CLIENTE_NIVEL_2', 24);
defined('NIVEL_ACESSO_CLIENTE_NIVEL_0')         || define('NIVEL_ACESSO_CLIENTE_NIVEL_0', 25);
