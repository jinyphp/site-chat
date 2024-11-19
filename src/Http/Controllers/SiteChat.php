<?php
namespace Jiny\Site\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

use Jiny\Site\Http\Controllers\SiteController;
class SiteChat extends SiteController
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        $this->actions['view']['layout'] = "jiny-site-chat::site.chat.layout";
    }

}
