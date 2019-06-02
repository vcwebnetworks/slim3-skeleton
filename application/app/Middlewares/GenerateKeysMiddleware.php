<?php

/**
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright 31/05/2019 Vagner Cardoso
 */

namespace App\Middlewares {

    use Core\Helpers\Str;
    use Slim\Http\Request;
    use Slim\Http\Response;

    /**
     * Class GenerateKeysMiddleware
     *
     * @package App\Middlewares
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class GenerateKeysMiddleware extends Middleware
    {
        /**
         * @param \Slim\Http\Request $request PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable $next Next middleware
         *
         * @return \Slim\Http\Response
         */
        public function __invoke(Request $request, Response $response, callable $next)
        {
            foreach (['APP_KEY', 'API_KEY', 'DEPLOY_KEY'] as $key) {
                $value = env($key, '');

                if (empty($value)) {
                    $random = Str::randomBytes(32);
                    $scaped = preg_quote("={$value}", '/');

                    file_put_contents(
                        APP_FOLDER.'/.env',
                        preg_replace(
                            "/^{$key}{$scaped}/m",
                            "{$key}=vcw_{$random}",
                            file_get_contents(APP_FOLDER.'/.env')
                        )
                    );
                }
            }

            $response = $next($request, $response);

            return $response;
        }
    }
}
