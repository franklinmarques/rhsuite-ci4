<?phpnamespace App\Filters;use App\Interfaces\TipoFuncionario;use CodeIgniter\Exceptions\PageNotFoundException;use CodeIgniter\HTTP\RequestInterface;use CodeIgniter\Security\Exceptions\SecurityException;class AccessLevelsFilter extends BaseFilter{    public function before(RequestInterface $request, $arguments = null)    {        if (in_array(TipoFuncionario::class, self::$interfaces)) {            self::$accessLevels = self::$controller::accessLevels();        }        if (self::$accessLevels and in_array(session('nivel'), self::$accessLevels) == false) {            if ($request->isAJAX()) {                throw SecurityException::forDisallowedAction();            }            throw PageNotFoundException::forPageNotFound();        }    }}