<?phpuse \glial\synapse\Controller;class WhoWeAre extends Controller{    function index()    {        $this->title = __("Who we are?");        $this->ariane = "> " . $this->title;    }}