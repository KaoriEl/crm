<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApacheAuthMiddleware
{
    /**
     * The users array.
     *
     * @var array
     */
    protected $users;

    /**
     * The authenticated user.
     *
     * @var string
     */
    protected $currentUser;

    /**
     * Create a new shield middleware class.
     *
     *
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = config('auth.basic_auth');
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $user
     *
     * @return mixed
     */
    public function handle($request, Closure $next, string $user = null)
    {

        $this->verify($request->getUser(), $request->getPassword(), $user);

        return $next($request);
    }

    /**
     * Get the user credentials array.
     *
     * @param string|null $user
     *
     * @return array
     */
    protected function getUsers(string $user = null): array
    {
        if ($user !== null) {
            return array_intersect_key($this->users, array_flip((array) $user));
        }

        return $this->users;
    }

    /**
     * Verify the user input.
     *
     * @param string|null $username
     * @param string|null $password
     * @param string|null $user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return null
     */
    public function verify(string $username = null, string $password = null, string $user = null)
    {

        if ($username && $password) {

            $users = $this->getUsers($user);

            foreach ($users as $user => $credentials) {
                if (
                    ($username == reset($credentials)) &&
                    ($password == end($credentials))
                ) {
                    $this->currentUser = $user;

                    return;
                }
            }

        }

        throw new UnauthorizedHttpException('Basic');
    }
}
