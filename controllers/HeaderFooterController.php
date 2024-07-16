<?php 
class HeaderFooterController extends Controller
{
    public function header()
    {
        
        echo $this->template->render("includes/header.html");
    }

    public function headerGuest()
    {
        
        echo $this->template->render("includes/header-guest.html");
    }

    public function footer()
    {
        
        echo $this->template->render("includes/footer.html");
    }
}
?>