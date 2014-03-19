<CODE

<?php

ini_set('short_open_tag', 0);
include('conditions.php');
?>



class Intranet
{

 private $path;

 public $booked;
 public $bookingno;
 public $checkedin;
 public $cardnumber;
 public $error;
 public $pin;
 public $numtimes;
 public $client;
 public $lb;
 public $sb;
 public $times;
 public $next;
  public $failure;
  public $poll_client;
 
 public $extend ;
 public $warning_1 ;
 public $warning_2 ;
 public $warning_1_text ;
 public $warning_2_text ;
 public $nexttimeslot ;



  function __construct()
 {
  global $CONNECT;
 
  $this->connect = $CONNECT;
  //$this->path = "session.php?pcno=" . $CONFIG->pcno ;
  $this->path = "session.php?";



 }
public function strip_header($response)
     {
    $xml = substr($response,strpos($response,"<?xml"));
    return $xml;
     }



     public function check()
 {

    $this->booked = "";
     $this->bookingno = "";
     $this->checkedin = "";
     $this->cardnumber = "";
     $this->error = "";
     $this->pin = "";
     $this->numtimes = "";
     $this->client = "";
     $this->lb = "";
    $this->sb = "";

     global $CONFIG;
    

     $vars = $this->path .  "event=check&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;
     $response = $this->connect->http($vars,5);
     if ($response === false)
         {
         $this->booked = "offline"; $this->error = "Network error";
       //  echo "error is " . $this->error . "/error
//";

         }
     else {
     $response = $this->strip_header($response);

     //echo "vars is $vars
//";
   echo $response;
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
     $this->booked = $xml->booked;
     $this->bookingno = $xml->bookingno;
     $this->checkedin = $xml->checkedin;
     $this->cardnumber = $xml->cardnumber;
     $this->error = $xml->error;
     $this->pin = $xml->pin;
     $this->numtimes = $xml->numtimes;
     $this->client = $xml->client;
     $this->lb = $xml->lb;
    $this->sb = $xml->sb;
    $this->poll_client = $xml->pollclient;
    $this->command = $xml->command;
    if ($this->command == "shutdown")
    {
    default_shutdown();
    }
   // print_r($xml);

    $this->times = array();

    if (isset($xml->numtimes) && $xml->numtimes > 0)
    {
      //  echo "zzzzzzz $xml->numtimes xxxx";
       // print_r($xml->time);
        $counter = 0;
       foreach ($xml->time as $value)
            {
          // echo "$value ";
           $this->times[$counter] = $value;
             $counter++;
            }
     } else {$this->times[0] == $xml->time;}
  // echo $this->times[1];
    //echo "intranet is .......";
 // print_r($this->times);
     
 }  else {$this->booked = "offline"; $this->error = "Server error";  }

 }
 }




  public function checkin()
 {
  global $CONFIG;

     $vars = $this->path .  "event=checkin&bookingno=" . $this->bookingno . "&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;
     if ($this->cardnumber != ""){ $vars .= "&barcode=" . $this->cardnumber;}
   //  echo $vars;
     $response = $this->connect->http($vars,5);
     
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);
     echo $response;
    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
    $this->auth = $xml->auth;
    $this->next = time()  + $xml->next + 2;
  

 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }

   public function book($time,$barcode)
 {
 global $CONFIG;

     $vars = $this->path .  "event=book&barcode=" . $barcode . "&time=" . $time . "&buffer=30&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;
  
    // echo $vars;
     $response = $this->connect->http($vars,5);
   //  echo $response;
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);

    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
    $this->auth = $xml->auth;
    $this->bookingno = $xml->bookingno;
    $this->failure = $xml->failure;


 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }



public function end_session()
 {

 global $CONFIG;

     $vars = $this->path .  "event=endbooking&bookingno=" . $this->bookingno . "&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;

   //  echo $vars;
     $response = $this->connect->http($vars,5);
     //echo $response;
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);

    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
   // $this->extend = $xml->extend;
  //  $this->warning_1 = $xml->warning_1;
  //  $this->warning_2 = $xml->warning_2;
  //  $this->warning_1_text = $xml->warning_1_text;
  //  $this->warning_2_text = $xml->warning_2_text;
//echo "warning_1 is $this->warning_1";


 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }


 public function extend()
 {

      global $CONFIG;
     $vars = $this->path .  "event=extend&bookingno=" . $this->bookingno . "&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;

   //  echo $vars;
     $response = $this->connect->http($vars,5);
     //echo $response;
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);

    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
   // $this->extend = $xml->extend;
   $this->next = time() + $xml->duration + 2;
  //  $this->warning_1 = $xml->warning_1;
  //  $this->warning_2 = $xml->warning_2;
  //  $this->warning_1_text = $xml->warning_1_text;
  //  $this->warning_2_text = $xml->warning_2_text;
//echo "warning_1 is $this->warning_1";


 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }



  public function next()
 {

      global $CONFIG;
     $vars = $this->path .  "event=next&bookingno=" . $this->bookingno . "&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;

   //  echo $vars;
     $response = $this->connect->http($vars,5);
     //echo $response;
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);

    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {

   $this->nexttimeslot = $xml->nexttimeslot;


 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }

 public function validate()
 {
     global $CONFIG;
     $vars = $this->path .  "event=validate&bookingno=" . $this->bookingno . "&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;

   //  echo $vars;
     $response = $this->connect->http($vars,5);
     echo $response;
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);

    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
    $this->auth = $xml->auth;
    $this->failure = $xml->failure;
    $this->next = time() + $xml->next + 2;
  



 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }


  public function session()
 {
     global $CONFIG;
     $vars = $this->path .  "event=session&bookingno=" . $this->bookingno . "&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;

   //  echo $vars;
     $response = $this->connect->http($vars,5);
     echo $response;
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);

    // sleep(100);
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
    $this->extend = $xml->extend;
  $this->warning_1 = $xml->warning_1;
  $this->warning_2 = $xml->warning_2;
  $this->warning_1_text = $xml->warning_1_text;
  $this->warning_2_text = $xml->warning_2_text;
  $this->next = time() + $xml->duration + 5;




 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }
















     public function authenticate($time,$password,$barcode,$mode)
 {

      global $CONFIG;
     $vars = $this->path .  "event=authenticate&time=$time&password=$password&barcode=$barcode&mode=$mode&pcno=" . $CONFIG->pcno . "&secret=" . $CONFIG->intranet;
     //echo $vars;
     $response = $this->connect->http($vars,5);
     if ($response === false) {$this->booked = "offline"; $this->error = "Network error";}
     else {
     $response = $this->strip_header($response);
    // echo $response;
     $xml = @simplexml_load_string($response);
     if ($xml)
     {
     $this->auth = $xml->auth;
     $this->failure = $xml->failure;
     $this->change = $xml->change;
     
    //print_r($xml);

    if (isset($xml->numtimes) && $xml->numtimes > 1)
    {
      //  echo "zzzzzzz $xml->numtimes xxxx";
       // print_r($xml->time);
        $counter = 0;
       foreach ($xml->time as $value)
            {
          // echo "$value ";
           $this->times[$counter] = $value;
             $counter++;
            }
     } else {$this->times[0] == $xml->time;}
  // echo $this->times[1];
    //echo "intranet is .......";
 // print_r($this->times);

 }  else {$this->booked = "offline"; $this->error = "Server error";}

 }
 }
}

class Application
{
    public $intranet;
    public $Windows;
    public $status;
    public $bookingno;
    public $next;
    public $warning_1;
    public $warning_2;
    
    public $time;
    public $barcode;
    public $html_text;
    public $poll_client;
    public $last_check;
    
    function __construct($INTRANET)
 {
    
    // global $PASSWORD;
   //  global $CONFIG;

    
    $this->intranet =  $INTRANET;

   
  

    global $APP_STATUS;
    
    $this->status = $APP_STATUS;

   








  // 

   
   $this->check_status();
   //$this->wnd->hide_all();
 } 
   public function check_status()
    {

        global $Windows;
        global $NEXTCHECK;

        $now = time();

        if ($this->poll_client == 1 && $this->status != "available")
        {
       $NEXTCHECK = $now + 120;
        }
        else
        {
         $NEXTCHECK = $now + 30;
        }
       
       if ($this->status == "clock")
       {
        $this->intranet->validate();
        $this->last_check = $now;
     //   echo "after validation intranet auth is " .$this->intranet->auth . "
     //       ";
        if ($this->intranet->auth == "no") {$this->status = "";}
       }
       
        if ($this->status != "clock")
       {
        $this->intranet->check();
        $this->last_check = $now;

           //echo "setting client client is " . $this->intranet->poll_client . "
//";
       }
    

  settype($this->bookingno, "integer");
  settype($this->intranet->bookingno, "integer");

  $this->next = $this->intranet->next;


  if (($this->status != "conditions" && $this->status != "clock" ) || $this->intranet->bookingno != $this->bookingno)
    {
        

      if ($this->intranet->client == 1)
      {
         default_kill_apps();
      }

    //if ($this->intranet->booked == "no")
    if ($this->intranet->booked == "no")
        {
        //echo "hello available";

        $min = 0;
        if ($this->intranet->times)
        {
         foreach ($this->intranet->times as $value)
            {
            if ($value > $min ) {$min = $value;}
            }
        }

        if ($min < 60)
        {
         $this->status = "next";
        $Windows['next']->on();

        foreach ($Windows as $key => $window)
                {
                if ($key != "next") {$window->off();}
                }

        }
        else
        {

        $this->status = "no";
        $Windows['available']->on();
       
        foreach ($Windows as $key => $window)
                {
                if ($key != "available") {$window->off();}
                }
        }
        }

      if ($this->intranet->booked == "offline")
        {
        //default_kill_apps();
        $this->status = "offline";
        $Windows['offline']->on();
          foreach ($Windows as $key => $window)
                {
               
                 if ($key != "offline") {
                    $window->off();}
                }
        }

    if ($this->intranet->booked == "yes")
        {
       //  $this->barcode();
       // default_kill_apps();
        $this->status = "yes";
        $Windows['barcode']->on();
         foreach ($Windows as $key => $window)
                {
                 if ($key != "barcode") {$window->off();}
                }
   
         }

     if ($this->intranet->booked == "pin")
        {
        // default_kill_apps();
         $this->status = "pin";
  
         $Windows['pin']->on();
          foreach ($Windows as $key => $window)
                {
                 if ($key != "pin") {$window->off();}
                }
        
       //  $this->pin();
         }



         if ($this->intranet->booked == "locked")
        {
         //default_kill_apps();
         $this->status = "locked";
  
         $Windows['locked']->on();
          foreach (Windows as $key => $window)
                {
                 if ($key != "locked") {$window->off();}
                }

       //  $this->pin();
         }


    }

            if ($this->intranet->client == 0 && $this->intranet->booked != "offline")
                {
                foreach ($Windows as $window)
                {
                $window->hide_all();
                }
                }

    }
    

    public function show_warning($warning)
    {

   $warning = new Warning($warning);
     
    }

     public function show_extend($warning)
    {

$extend = new Extend($warning);


    }

  public function end_session_confirm()
{
    //show dialog  
     $confirm = new EndConfirm();

}

public function extend()
{



$this->intranet->extend();
$this->next = $this->intranet->next;

}

public function end_session()
{


  $this->intranet->end_session();
 $this->check_status();
}

  public function book()
    {
    global $Windows;

    $this->intranet->book($this->time,$this->barcode);


    
     if ($this->intranet->auth == "yes")
     {
  //    echo "setting clock";

        $this->status = "clock";
        $this->bookingno = $this->intranet->bookingno;

         $this->intranet->session();

         $this->next = $this->intranet->next;

           echo "intranet->next is " . $this->intranet->next . " this->next is ". $this->next;

         $this->warning1 = $this->intranet->next - $this->intranet->warning_1;
         $this->warning2 = $this->intranet->next - $this->intranet->warning_2;

        $Windows['clock']->on();
          foreach ($Windows as $key => $window)
                {
                 if ($key != "clock") {$window->off();}
                }

               // echo "sleeping now";
               
        // $this->show_warning("session ends in VVV");
        // echo "turning on window";
         
           

        


     } else { $this->check_status();}
   //  echo "XXXX app status is after booking is " . $this->status . "YYYYYY
//";
    }

    public function checkin()
    {
          global $Windows;
   // echo "ZZZZZZZZZZZZZZZZZZZZZZZZ";
     $this->intranet->checkin();

  //   echo "auth is " . $this->intranet->auth . "MMM";

     if ($this->intranet->auth == "yes")
     {
        $this->next = $this->intranet->next;
        $this->status = "clock";
        $Windows['clock']->on();
          foreach ($Windows as $key => $window)
                {
                 if ($key != "clock") {$window->off();}
                }
          
        // $this->show_warning("session ends in VVV");
        // echo "turning on window";

         $this->intranet->session();

         $this->warning1 = $this->intranet->next - $this->intranet->warning_1;
         $this->warning2 = $this->intranet->next - $this->intranet->warning_2;


     } else { $this->check_status();}
    }

    public function conditions()
    {
  global $Windows;

      
         $Windows['conditions']->on();
          foreach ($Windows as $key => $window)
                {
                 if ($key != "conditions") {$window->off();}
                }

       //  $this->pin();

    }
     

}

Class Warning extends GtkDialog
{


  function __construct($warning) {
     	parent::__construct();

   $this->set_size_request(400,140);
    $this->set_position(GTK_WIN_POS_CENTER);
    $this->set_title('Session expiry');
   
settype($warning, "string");
   // echo "ZZz $warning ZZZZZ";
   // $text = $warning;
    $label = new GtkLabel($warning);
    //label->set_text($warning);
    $label->modify_font(new PangoFontDescription('Arial 16'));
   // $vbox = new GtkVBox();
   // $dialog->add($vbox);
    $fixed = new Gtkfixed();
    $this->vbox->pack_start($fixed); //
    $button_ok = new GtkButton(" OK ");
    $fixed->put($label,10,10);
    $fixed->put($button_ok,10,60);
    //$button_ok->set_size_request(80,40);
    $button_ok_label = $button_ok->get_child(); // note 1
    $button_ok_label->modify_font(new PangoFontDescription('Arial 16'));
    //$dialog->vbox->pack_start($button_ok,0,0);
    $button_ok->connect('clicked',array($this,'close'));
    $this->show_all();
    $this->set_keep_above(TRUE);
    $closewarning = Gtk::timeout_add(20000, array($this,'close'));
  }

  public function close()
  {
     // echo "closing";
      $this->destroy();
  }
}


Class EndConfirm extends GtkDialog
{


  function __construct() {
     	parent::__construct();

   $this->set_size_request(440,140);
    $this->set_position(GTK_WIN_POS_CENTER);
    $this->set_title('End session');
       
settype($warning, "string");
   // echo "ZZz $warning ZZZZZ";
   // $text = $warning;
    $label = new GtkLabel("Are you sure you want to end this session?");
    //label->set_text($warning);
    $label->modify_font(new PangoFontDescription('Arial 16'));
   // $vbox = new GtkVBox();
   // $dialog->add($vbox);
    $fixed = new Gtkfixed();
    $this->vbox->pack_start($fixed); //
    $button_ok = new GtkButton(" OK ");
    $fixed->put($label,10,20);


    $button_yes = new GtkButton(" Yes ");
    $button_no = new GtkButton(" No ");

    $fixed->put($button_no,10,70);
    $fixed->put($button_yes,80,70);

    //$button_yes->set_size_request(80,40);
    //$button_no->set_size_request(80,40);
    $button_yes_label = $button_yes->get_child(); // note 1
    $button_yes_label->modify_font(new PangoFontDescription('Arial 16'));
    $button_no_label = $button_no->get_child(); // note 1
    $button_no_label->modify_font(new PangoFontDescription('Arial 16'));

    //$dialog->vbox->pack_start($button_ok,0,0);
    $button_no->connect('clicked',array($this,'close'));
    $button_yes->connect('clicked',array($this,'end'));
    //$dialog->vbox->pack_start($button_ok,0,0);

    $this->show_all();
     $this->set_keep_above(TRUE);
    $closewarning = Gtk::timeout_add(20000, array($this,'close'));
  }

  public function end()
  {
      $this->destroy();
      global $APPLICATION;
      $APPLICATION->end_session();
      
  }

  public function close()
  {
     // echo "closing";
      $this->destroy();
  }
}




Class Extend extends GtkDialog
{


  function __construct($warning) {
     	parent::__construct();

   $this->set_size_request(400,180);
    $this->set_position(GTK_WIN_POS_CENTER);
    $this->set_title('Extend booking?');
        
    settype($warning, "string");
    $label1 = new GtkLabel($warning);
    $label1->modify_font(new PangoFontDescription('Arial 16'));
    $label2 = new GtkLabel("Would you like to extend your booking?");
    $label2->modify_font(new PangoFontDescription('Arial 16'));
   // $vbox = new GtkVBox();
   // $dialog->add($vbox);
    $fixed = new Gtkfixed();
    $this->vbox->pack_start($fixed); //
    $button_yes = new GtkButton(" Yes ");
    $button_no = new GtkButton(" No ");
    $fixed->put($label1,10,10);
    $fixed->put($label2,10,50);
    $fixed->put($button_yes,10,100);
    $fixed->put($button_no,80,100);

    //$button_yes->set_size_request(80,40);
    //$button_no->set_size_request(80,40);
    $button_yes_label = $button_yes->get_child(); // note 1
    $button_yes_label->modify_font(new PangoFontDescription('Arial 16'));
    $button_no_label = $button_no->get_child(); // note 1
    $button_no_label->modify_font(new PangoFontDescription('Arial 16'));

    //$dialog->vbox->pack_start($button_ok,0,0);
    $button_no->connect('clicked',array($this,'close'));
    $button_yes->connect('clicked',array($this,'extend'));
    $this->show_all();
    $this->set_keep_above(TRUE);
    $closewarning = Gtk::timeout_add(20000, array($this,'close'));
  }
public function extend()
{
    $this->destroy();
    global $APPLICATION;
    $APPLICATION->extend();
    
}


  public function close()
  {
     // echo "closing";
      $this->destroy();
  }
}










class WinClock extends GtkWindow {
    public $lbl;
    public $statusicon;

     function __construct() {
     	parent::__construct();

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#C0C0C0"));
       $this->lbl = new GtkLabel('Time left 121:40');
       $this->lbl->modify_font(new PangoFontDescription('Arial 32'));
       $style= $this->style->copy();
       $style->bg[Gtk::STATE_NORMAL] = $style->white;
       $this->set_style($style);
       $this->set_title("Time left 10:30");
       $this->set_size_request(400, 180);
       
       
        $screen = $this->get_screen(); // note 1
        $screen_width = $screen->get_width();
        $screen_height = $screen->get_height();
        $this->set_uposition($screen_width - 450,$screen_height - 400);

        $button_end = new GtkButton(" End session ");
        $button_end->connect('clicked',array($this,'end'));
        $button_end_label = $button_end->get_child(); // note 1
        $button_end_label->modify_font(new PangoFontDescription('Arial 18'));

        $fixed = new GtkFixed();
        $this->add($fixed);
        $fixed->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#FF2B3C"));

        $vbox = new GtkVBOx();
        $vbox->set_size_request(370, 150);
      $vbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#FF2B3C"));

        $fixed->put($vbox,15,15);

        $frame = new GtkFrame();
        $frame->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#000000"));
       
        $vbox->pack_start($frame, true,true);
        
      
        
        $fixed2 = new GtkFixed();
        $fixed2->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#C9B2FF"));
        $fixed2->put($this->lbl,10,30);
        $fixed2->put($button_end,10,100);
        $eventbox = new GtkEventBox();
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#E0E0E0"));
        $frame->add($eventbox);
        $eventbox->add($fixed2);
   
        $this->set_decorated(false);
   }

   public function end()
   {
    global $APPLICATION;
    $APPLICATION->end_session_confirm();

   }



   public function update($secondsleft)
   {
    
    $temp = $secondsleft / 60;
    $minutes = floor($temp);

  //     echo "seconds left $secondsleft minutes is $minutes
//";

    $seconds = $secondsleft % 60;
    if ($seconds < 10) {$seconds = "0" . $seconds;}
    $newtext = "Time left " . $minutes . ":" . $seconds;
    $this->lbl->set_text($newtext);
    $this->set_title($newtext);
   }

     public function on()
     {
   // echo "showing winpin";
         global $APPLICATION;
         $secondsleft = $APPLICATION->next - time();


    $this->update($secondsleft);
         $this->show_all();
         global $windowOnTop;
        // $windowOnTop = $this;

       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {
         $this->hide_all();
         $this->set_keep_above(FALSE);
     }




}


class WinLocked extends GtkWindow
{
    private $window;



   // private $cardnum;
   // private $label_card_number;

    function __construct()
     {
     parent::__construct();



      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //
        $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#B0C4DE"));

        $eventbox = new GtkEventBox();
        $eventbox->set_size_request(1000,600);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $label_booked = new GtkLabel("Staff assistance required");
        $label_booked->modify_font(new PangoFontDescription('Arial 58'));
        
        $label_message = new GtkLabel("Please ask library staff for a booking to use this computer.");
        $label_message->modify_font(new PangoFontDescription('Arial 16'));

        //$label_card = new GtkLabel("Library card number");
        //$label_card->modify_font(new PangoFontDescription('Arial 18'));

       // $cardnum = "****" . substr($this->intranet->cardnumber,-4);
       // $this->label_card_number = new GtkLabel($cardnum);
      //  $this->label_card_number->modify_font(new PangoFontDescription('Arial 18'));






         $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));

	$vbox = new GtkVBox();
	$vbox->pack_start($label_booked);
	$vbox->pack_start($label_message);

       // $eventfixed = new GtkFixed ();
         //$dummy = new GtkLabel("Time available");
       // $eventfixed->put($label_booked,165,250);
     //   $eventfixed->put($label_card,280,250);
      //  $eventfixed->put($this->label_card_number,500,250);


        $eventbox->add($vbox);

        $fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),(($this->screen_height /2) -300));
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);
         $this->connect('key-press-event', 'onKeyPress');

     }





     public function on()
     {
   // echo "showing winpin";
         $this->show_all();
         global $windowOnTop;
         $windowOnTop = $this;

       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {
         $this->hide_all();
         $this->set_keep_above(FALSE);
     }

}

class WinNext extends GtkWindow
{
    private $window;



   // private $cardnum;
   // private $label_card_number;

    function __construct()
     {
     parent::__construct();



      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //
        $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#99B0FF"));

        $eventbox = new GtkEventBox();
        $eventbox->set_size_request(1000,400);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $label_booked = new GtkLabel("Next session begins shortly");
        $label_booked->modify_font(new PangoFontDescription('Arial 50'));

        //$label_card = new GtkLabel("Library card number");
        //$label_card->modify_font(new PangoFontDescription('Arial 18'));

       // $cardnum = "****" . substr($this->intranet->cardnumber,-4);
       // $this->label_card_number = new GtkLabel($cardnum);
      //  $this->label_card_number->modify_font(new PangoFontDescription('Arial 18'));



 


         $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));



        $vbox = new GtkVbox ();
         //$dummy = new GtkLabel("Time available");
        $vbox->pack_start($label_booked);
     //   $eventfixed->put($label_card,280,250);
      //  $eventfixed->put($this->label_card_number,500,250);


        $eventbox->add($vbox);

        $fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),(($this->screen_height /2) -200));
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);
         $this->connect('key-press-event', 'onKeyPress');

     }





     public function on()
     {
   // echo "showing winpin";
         $this->show_all();
         global $windowOnTop;
         $windowOnTop = $this;

       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {
         $this->hide_all();
         $this->set_keep_above(FALSE);
     }

}


class WinPin extends GtkWindow
{
    private $window;
    private $intranet;
    private $entrypin;
    private $label_error;

 

   // private $cardnum;
   // private $label_card_number;

    function __construct($intranet)
     {
     parent::__construct();
     

     
     $this->intranet = $intranet;
  
     
      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //
        $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#005fad"));

        $eventbox = new GtkEventBox();
        $eventbox->set_size_request(1000,600);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $label_booked = new GtkLabel("Computer booked");
        $label_booked->modify_font(new PangoFontDescription('Arial 58'));

        //$label_card = new GtkLabel("Library card number");
        //$label_card->modify_font(new PangoFontDescription('Arial 18'));

       // $cardnum = "****" . substr($this->intranet->cardnumber,-4);
       // $this->label_card_number = new GtkLabel($cardnum);
      //  $this->label_card_number->modify_font(new PangoFontDescription('Arial 18'));

        $label_pin = new GtkLabel("PIN");
        $label_pin->modify_font(new PangoFontDescription('Arial 18'));

        $this->entry_pin = new GtkEntry('',100);
        $this->entry_pin->modify_font(new PangoFontDescription('Arial 18'));
        $this->entry_pin->set_visibility(false);

        $button_book = new GtkButton(" Log in ");
        $button_label = $button_book->get_child(); // note 1
        $button_label->modify_font(new PangoFontDescription('Arial 18'));
        $button_book->connect('clicked',array($this,'authenticate'));


         $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));

         $label_message1 = new GtkLabel("This booking has a one off PIN.");
         $label_message1->modify_font(new PangoFontDescription('Arial 16'));

         $label_message2 = new GtkLabel("To obtain the PIN number please ask library staff.");
         $label_message2->modify_font(new PangoFontDescription('Arial 16'));

         $this->label_error = new GtkLabel("");
         $this->label_error->modify_font(new PangoFontDescription('Arial 24'));
         $this->label_error->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse('#D50A0A'));

         
         $label_blank = new GtkLabel("");
         $label_blank->modify_font(new PangoFontDescription('Arial 16'));


        $eventfixed = new GtkFixed ();
 
       // $eventfixed->put($label_booked,240,150);
  
        $eventfixed->put($label_pin,380,50);
        $eventfixed->put($this->entry_pin,500,50);
        $eventfixed->put($button_book,500,100);
       // $eventfixed->put($this->label_error,400,100);
       // $eventfixed->put($label_message1,400,480);
       // $eventfixed->put($label_message2,340,520);
        
        $vbox = new GtkVBox();
        $vbox->pack_start($label_booked,false,false,100);
        $vbox->pack_start($eventfixed);
        $vbox->pack_start($this->label_error);
        $vbox->pack_start($label_message1);
        $vbox->pack_start($label_message2);
         $vbox->pack_start($label_blank);
      
        

        $eventbox->add($vbox);

        $fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),(($this->screen_height /2) -300));
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);
        $this->connect('key-press-event', 'onKeyPress');
        $this->connect('key-press-event',array($this, 'check_key'));
       // $button_book->connect('activate',array($this, 'authenticate'));

     }

        public function check_key($widget,$event)
        {
       

        if ($event->keyval == 65293)
            {
            $this->authenticate();
            }
        }
    
     public function authenticate()
     {
   
       $password = $this->entry_pin->get_text();
       if (trim($password) != "")
       {
          if ($password == $this->intranet->pin)
          {
           $this->label_error->set_text("");
           // echo "passwords match";
            global $APPLICATION;
            $APPLICATION->status = "conditions";
            $APPLICATION->bookingno = $this->intranet->bookingno;
            $this->entry_pin->set_text('');
            $APPLICATION->conditions();
          }
        else 
          {

             $this->label_error->set_text("  Invalid PIN!");

             /*
                $dialog = new GtkDialog('Error', null, Gtk::DIALOG_MODAL); // create a new dialog
                $dialog->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
                $top_area = $dialog->vbox; // note 2
                $top_area->pack_start($hbox = new GtkHBox()); // note 3
                $stock = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_WARNING,
                Gtk::ICON_SIZE_DIALOG); // note 4
                $hbox->pack_start($stock, 0, 0); // stuff in the icon
                $hbox->pack_start(new GtkLabel('Incorrect PIN')); // and the msg
                $dialog->add_button(Gtk::STOCK_OK, Gtk::RESPONSE_OK); // note 5
                $dialog->set_has_separator(false); // don't display the set_has_separator
                $dialog->show_all(); // show the dialog
                $dialog->run(); // the dialog in action
                $dialog->destroy(); // done. close the dialog box.
               //
               $this->entry_pin->set_text('');
               $this->entry_pin->grab_focus();
              * */
              
           
            
          }
    
        }
        else {$this->label_error->set_text("No PIN entered");}
     }
      
     
     public function on()
     {
   // echo "showing winpin";
         $this->show_all();
         global $windowOnTop;
         $windowOnTop = $this;

       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {

         $this->label_error->set_text("");
         $this->entry_pin->set_text('');
       
         $this->hide_all();
         $this->set_keep_above(FALSE);
     }

}






class WinOffline extends GtkWindow
{
    private $window;
    private $intranet;
    private $label_message1;
    private $label_offline;
    private $label_closed;


   // private $cardnum;
   // private $label_card_number;

    function __construct($intranet)
     {
     
     parent::__construct();
     
 
     $this->intranet = $intranet;
   

      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //
       $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

   $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#d3281f"));
        //$this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#5C26FF"));
     
        $eventbox = new GtkEventBox();
        $eventbox->set_size_request(1000,600);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $this->label1 = new GtkLabel("");
        $this->label1->modify_font(new PangoFontDescription('Arial 58'));

        $this->label2 = new GtkLabel("");
        $this->label2->modify_font(new PangoFontDescription('Arial 58'));

      //  $label_card = new GtkLabel("Library card number");
       // $label_card->modify_font(new PangoFontDescription('Arial 18'));

       // $cardnum = "****" . substr($this->intranet->cardnumber,-4);
       // $this->label_card_number = new GtkLabel($cardnum);
      //  $this->label_card_number->modify_font(new PangoFontDescription('Arial 18'));

      


         $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));

        
        
         $this->label_message1 = new GtkLabel("");
         $this->label_message1->modify_font(new PangoFontDescription('Arial 24'));

    

       // $eventfixed = new GtkFixed ();
         //$dummy = new GtkLabel("Time available");
       // $eventfixed->put($this->label_offline,240,150);

        
     //   $eventfixed->put($label_card,280,250);
      //  $eventfixed->put($this->label_card_number,500,250);

      $vbox1 = new GtkVbox();
       $vbox1->set_size_request(1000,400);
     
       $vbox1->pack_start($this->label1);
      $vbox1->pack_start($this->label2);

       $eventbox->add($vbox1);

        $fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),(($this->screen_height /2) -300));
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);
        $this->connect('key-press-event', 'onKeyPress');
            

     }

     public function on()
     {
       
         settype($this->intranet->error,'string');

         if ($this->intranet->error == "Library closed")
         {
            $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#D3D3D3"));
          $this->label1->set_text("Library closed");
          $this->label2->set_text("Computer not available");
        
         }

          elseif (strpos(strtolower($this->intranet->error),"out of order") != false)
         {
            $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#d3281f"));
          $this->label1->set_text("Out of order");
          $this->label2->set_text("Computer not available");
          
         }
         else
         {

           $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#d3281f"));
        $this->label1->set_text("Computer offline");
     
        $this->label2->set_text($this->intranet->error);
         }
         $this->show_all();
      
         $this->present();
         global $windowOnTop;
         $windowOnTop = $this;
        //  sleep(100);
       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {
         $this->hide_all();
         $this->set_keep_above(FALSE);
     }

}




class WinConditions extends GtkWindow
{
    private $window;
    private $intranet;
    public  $html;
    private $scrolled_win;
    private $eventfixed;
    public $html_text;
    private $handle;
    private $vport ;


   // private $label_card_number;

    function __construct($intranet)
     {
     parent::__construct();
     
  
     $this->intranet = $intranet;

      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //


        $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#AAD0FF"));

        $eventbox = new GtkEventBox();
      //  $eventbox->set_size_request(round($this->screen_width * 0.8),800);
      //  $eventboxwidth= round($this->screen_width * 0.8);
        $eventbox->set_size_request(1000,800);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $label_booked = new GtkLabel("Computer booked");
        $label_booked->modify_font(new PangoFontDescription('Arial 58'));

        $this->scrolled_win = new GtkScrolledWindow();
        $this->scrolled_win->set_size_request(800,600);
      

        $button_ok = new GtkButton(" I accept ");
        $button_ok_label = $button_ok->get_child(); // note 1
        $button_ok_label->modify_font(new PangoFontDescription('Arial 18'));
        

        $button_cancel = new GtkButton(" Cancel ");
        $button_cancel_label = $button_cancel->get_child(); // note 1
        $button_cancel_label->modify_font(new PangoFontDescription('Arial 18'));
        $button_cancel->connect('clicked',array($this,'cancel'));

         $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));

        $this->eventfixed = new GtkFixed ();
         //$dummy = new GtkLabel("Time available");

     //   $eventfixed->put($label_card,280,250);
      //  $eventfixed->put($this->label_card_number,500,250);
       // $eventfixed->put($label_pin,280,300);
       $this->eventfixed->put($this->scrolled_win,100,100);
        $this->eventfixed->put($button_cancel,380,740);
        $this->eventfixed->put($button_ok,500,740);

        $eventbox->add($this->eventfixed);

        $fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),(($this->screen_height /2) -400));
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);
        $this->connect('key-press-event', 'onKeyPress');
        $this->connect('key-press-event',array($this, 'check_key'));
        $button_ok->connect('clicked',array($this,'checkin'));
       // $button_book->connect('activate',array($this, 'authenticate'));

 
 
    
 global $conditions;      


$conditions = wordwrap($conditions,100);
        
    $label_conditions = new GtkLabel("Conditions of use");
$label_conditions->set_markup($conditions);
$this->vport = new GtkViewport();
$this->vport->add($label_conditions);
$this->scrolled_win->add($this->vport);    
     

     }

        public function check_key($widget,$event)
        {


        if ($event->keyval == 65293)
            {
            $this->checkin();
            }
        }
     

      public function checkin()
     {
         global $APPLICATION;
         //$APPLICATION->status = '';
        // @$this->scrolled_win->remove($this->html);

       
         if ($APPLICATION->intranet->booked == "no")
         {
         $APPLICATION->book();//

         }
            elseif ($APPLICATION->intranet->booked == "offline")
        {
            $APPLICATION->status = "offline";
            global $Windows;

          $Windows['offline']->on();
          foreach ($Windows as $key => $window)
                {

                 if ($key != "offline") {
                    $window->off();}
                }
        }
         else
         {
           //  echo "application checking in";
          $APPLICATION->checkin();//
          
         }

     }
     public function cancel()
     {
         global $APPLICATION;
         $APPLICATION->status = '';
       //  @$this->scrolled_win->remove($this->html);
         $APPLICATION->check_status();//

     }
 

   

     public function on()
     {
       //  $this->html = @new GtkHTML();
         // $html_text = file_get_contents("http://192.168.10.11/intranet/main/conditions.htm");

           global $APPLICATION;
   // $this->html->load_from_string($APPLICATION->html_text);
     //  $this->scrolled_win = new GtkScrolledWindow();
      // $this->scrolled_win->set_size_request(800,600);

    //   $this->eventfixed->put($this->scrolled_win,100,100);
      // $this->vport->set_placement(GTK_CORNER_TOP_LEFT);
       $this->vport->step = 0;

         $this->show_all();
         global $windowOnTop;
         $windowOnTop = $this;

       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {
         $this->hide_all();
         
         $this->set_keep_above(FALSE);
     }

}





class WinBarcode extends GtkWindow
{
    private $window;
    private $intranet;
    private $cardnum;
    private $label_card_number;
    private $entry_pin;
    private $button_book;
    private $label_error;



    function __construct($intranet)
     {
     parent::__construct();
     $this->intranet = $intranet;
  
  

      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //
        $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#FF873D"));

        $eventbox = new GtkEventBox();
        $eventbox->set_size_request(1000,600);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $label_booked = new GtkLabel("Computer booked");
        $label_booked->modify_font(new PangoFontDescription('Arial 58'));

        $label_card = new GtkLabel("Library card number");
        $label_card->modify_font(new PangoFontDescription('Arial 18'));

        $cardnum = "****" . substr($this->intranet->cardnumber,-4);
        $this->label_card_number = new GtkLabel($cardnum);
        $this->label_card_number->modify_font(new PangoFontDescription('Arial 18'));

        $label_pin = new GtkLabel("PIN");
        $label_pin->modify_font(new PangoFontDescription('Arial 18'));

        $this->entry_pin = new GtkEntry('',100);
        $this->entry_pin->modify_font(new PangoFontDescription('Arial 18'));
        $this->entry_pin->set_visibility(false);

        $this->button_book = new GtkButton(" Log in ");
        $button_label = $this->button_book->get_child(); // note 1
        $button_label->modify_font(new PangoFontDescription('Arial 18'));


         $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));

        $this->label_error = new GtkLabel("");
         $this->label_error->modify_font(new PangoFontDescription('Arial 24'));
         $this->label_error->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse('#D50A0A'));






        $eventfixed = new GtkFixed ();
         //$dummy = new GtkLabel("Time available");
       // $eventfixed->put($label_booked,200,150);
        $eventfixed->put($label_card,280,50);
        $eventfixed->put($this->label_card_number,520,50);
        $eventfixed->put($label_pin,280,100);
        $eventfixed->put($this->entry_pin,520,100);
        $eventfixed->put($this->button_book,520,150);
        //$eventfixed->put($this->label_error,410,480);

        $vbox = new GtkVBox();
        $vbox->pack_start($label_booked);
        $vbox->pack_start($eventfixed);
        $vbox->pack_start($this->label_error);
        
        $eventbox->add($vbox);

        $fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),200);
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);
        $this->connect('key-press-event', array($this,'check_key'));
        $this->button_book->connect('clicked', array($this,'authenticate'));


            $this->connect('key-press-event', 'onKeyPress');

     }

       public function check_key($widget,$event)
        {


        if ($event->keyval == 65293)
            {
            $this->authenticate();
            }
        }

     public function authenticate()
     {
        
       $password = $this->entry_pin->get_text();

       if ($password != "")
       {
       $this->button_book->set_sensitive(false);
       
       global $APPLICATION;
       $APPLICATION->intranet->authenticate("1",$password,$APPLICATION->intranet->cardnumber,"check");

       if ($APPLICATION->intranet->auth == "yes")
       {
         $this->label_error->set_text("");

            //echo "passwords match";
           
            $APPLICATION->status = "conditions";
            $APPLICATION->bookingno = $this->intranet->bookingno;
            $this->entry_pin->set_text('');
             $this->button_book->set_sensitive(true);
            $APPLICATION->conditions();
          }
          elseif ($APPLICATION->intranet->booked == "offline")
        {
              $this->label_error->set_text("");

            $APPLICATION->status = "offline";
            global $Windows;
          $Windows['offline']->on();
          foreach ($Windows as $key => $window)
                {

                 if ($key != "offline") {
                    $window->off();}
                }
        }
        else
          {
            $this->label_error->set_text("   Invalid PIN");
            $this->button_book->set_sensitive(true);
            /*
                $dialog = new GtkDialog('Error', null, Gtk::DIALOG_MODAL); // create a new dialog
                $dialog->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
                $top_area = $dialog->vbox; // note 2
                $top_area->pack_start($hbox = new GtkHBox()); // note 3
                $stock = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_WARNING,
                Gtk::ICON_SIZE_DIALOG); // note 4
                $hbox->pack_start($stock, 0, 0); // stuff in the icon
                

             //   $text = settype($APPLICATION->intranet->failure,"string");
               // echo "failure is $text TTTTT " . $APPLICATION->intranet->failure ."SSSSSSSSSS";
                $hbox->pack_start(new GtkLabel("Invalid PIN")); // and the msg
                $dialog->add_button(Gtk::STOCK_OK, Gtk::RESPONSE_OK); // note 5
                $dialog->set_has_separator(false); // don't display the set_has_separator
                $dialog->show_all(); // show the dialog
                $dialog->run(); // the dialog in action
                $dialog->destroy(); // done. close the dialog box.
               //
               $this->entry_pin->set_text('');
                $this->button_book->set_sensitive(true);
               $this->entry_pin->grab_focus();

             */
           
            
          }
       }
       else
       {
            $this->label_error->set_text("No PIN entered");
       }

        
     }




     public function on()
     {
         $this->cardnum = "****" . substr($this->intranet->cardnumber,-4);
         $this->label_card_number->set_text($this->cardnum);
         $this->show_all();
         global $windowOnTop;
         $windowOnTop = $this;
       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {

         $this->label_error->set_text("");
         $this->entry_pin->set_text(''); 

         $this->hide_all();
         $this->set_keep_above(FALSE);
     }

}

class WinAvailable extends GtkWindow
{
    private $window;
    private $intranet;
    private $model;
    private $combobox;
    private $button_book;
    private $entry_barcode;
    private $entry_pin;
    private $label_error;

    
 

    function __construct($intranet)
     {
     parent::__construct();
     
 
     $this->intranet = $intranet;
     
 
      $this->fullscreen();
        $screen = $this->get_screen(); // note 1
      //print_r($this);
      //
      //
        $screen_width = $screen->get_width();
       $screen_height = $screen->get_height();
       $this->screen_width = $screen_width ;
       $this->screen_height = $screen_height;
       $this->set_skip_taskbar_hint(true);

       //echo "width is $width height is height";
        //$wi->set_size_request($screen_width*2/3, $screen_height*2/3);

        $this->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#A7D051"));

        $eventbox = new GtkEventBox();
        $eventbox->set_size_request(1000,600);
        $eventbox->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse('#ffffff'));


        $shutdown_button = new GtkButton(" Shutdown ");
	$shutdown_button->connect('clicked','default_shutdown');
	$shutdown_button->set_size_request(140,50);
	$button_label = $shutdown_button->get_child(); // note 1
	$button_label->modify_font(new PangoFontDescription('Arial 18'));


         $label_available = new GtkLabel("Computer available");
         $label_available->modify_font(new PangoFontDescription('Arial 58'));
         $label_time = new GtkLabel("Time available");
         $label_time->modify_font(new PangoFontDescription('Arial 18'));
         $this->combobox = new GtkComboBox();


        $eventfixed = new GtkFixed();
     //   $eventfixed->put($label_available,100,150);
      


	$fixed = new GtkFixed ();
	$fixed->put($eventbox,(($this->screen_width /2) -500),200);
	$fixed->put($shutdown_button,10,$screen_height - 60);

	$this->add($fixed);

// Create a model
if (defined("GObject::TYPE_STRING")) {
    $this->model = new GtkListStore(GObject::TYPE_STRING);
} else {
    $this->model = new GtkListStore(Gtk::TYPE_STRING);
}

// Set up the combobox
$this->combobox->set_model($this->model); // note 1
$cellrenderer = new GtkCellRendererText(); // note 2
$cellrenderer->set_property('font',  'Arial 16');

$this->combobox->pack_start($cellrenderer);
$this->combobox->set_attributes($cellrenderer, 'text', 0); // note 3

// Stuff the choices in the model


$this->combobox->set_size_request(150,-1);
       //  print_r($this->intranet->times);
         $label_barcode = new GtkLabel("Library barcode");
         $label_barcode->modify_font(new PangoFontDescription('Arial 18'));
         $this->entry_barcode = new GtkEntry('',100);
         $this->entry_barcode->modify_font(new PangoFontDescription('Arial 18'));
         $this->entry_barcode->set_size_request(230,-1);
         $label_pin = new GtkLabel("PIN");
         $label_pin->modify_font(new PangoFontDescription('Arial 18'));
         $this->entry_pin = new GtkEntry('',100);
         $this->entry_pin->modify_font(new PangoFontDescription('Arial 18'));
         $this->entry_pin->set_visibility(false);
         $this->button_book = new GtkButton(" Book now ");
         $button_label = $this->button_book->get_child(); // note 1
         $button_label->modify_font(new PangoFontDescription('Arial 18'));
         //$button_book->set_size_request(50,50);

        // $helloworld = "<h1>Hello World</h1>";
       //  $html = new GtkHTML();
       //  $html->load_from_string($hello_world);

         $this->label_error = new GtkLabel("");
         $this->label_error->modify_font(new PangoFontDescription('Arial 24'));
         $this->label_error->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse('#D50A0A'));


         $label_message1 = new GtkLabel("To book a computer, please enter a library PIN number.");
         $label_message1->modify_font(new PangoFontDescription('Arial 16'));


         $label_message2 = new GtkLabel("To obtain a PIN number please ask library staff.");
         $label_message2->modify_font(new PangoFontDescription('Arial 16'));
         
         $label_blank = new GtkLabel("");
         $label_blank->modify_font(new PangoFontDescription('Arial 16'));


         $eventfixed = new GtkFixed ();
      
       // $eventfixed->put($label_available,100,150);
        $eventfixed->put($label_time,280,50);
        $eventfixed->put($this->combobox,500,50);
        $eventfixed->put($label_barcode,280,100);
        $eventfixed->put($this->entry_barcode,500,100);
        $eventfixed->put($label_pin,280,150);
        $eventfixed->put($this->entry_pin,500,150);
        $eventfixed->put($this->button_book,500,200);
      //  $eventfixed->put($this->label_error,320,450);
       // $eventfixed->put($label_message1,280,500);
       // $eventfixed->put($label_message2,310,530);


         //$eventbox->add($eventfixed);
         $vbox = new GtkVBox();
        // spacer($vbox, 20); 
         $vbox->pack_start($label_available,false,false,40);
         $vbox->pack_start($eventfixed);
         $vbox->pack_start($this->label_error);
       
         $vbox->pack_start($label_message1);
         $vbox->pack_start($label_message2);
          $vbox->pack_start($label_blank);
         $eventbox->add($vbox);
 


         $this->connect('key-press-event', 'onKeyPress');
         $this->connect('key-press-event', array($this,'check_key'));
         $this->button_book->connect('clicked', array($this,'authenticate'));
     }


        public function check_key($widget,$event)
        {


        if ($event->keyval == 65293)
            {
            $this->authenticate();
            }
        }

     public function authenticate()
     {
$this->button_book->set_sensitive(false);

       $password = $this->entry_pin->get_text();
       $barcode = $this->entry_barcode->get_text();

       if ($barcode == "")

       {
      // $this->popup("Cardnumber not entered!");
      $this->label_error->set_text(" Cardnumber not entered!");

       }

       elseif ($password == "")
       {
       // $this->popup("PIN not entered!");
        $this->label_error->set_text("        PIN not entered!");
       }
     
       else
       {
       $this->label_error->set_text("");

       global $APPLICATION;
       $APPLICATION->intranet->authenticate("1",$password,$barcode,"new");

       if ($APPLICATION->intranet->auth == "yes")
       {
            $time = $this->model->get_value($this->combobox->get_active_iter(), 0) * 60;
            //echo "passwords match";
            $APPLICATION->next = time() + $time;
          
            $APPLICATION->time = $time;
            $APPLICATION->barcode = $barcode;


           //echo "intranet next is " . $APPLICATION->intranet->next . " cardnumber is " . $APPLICATION->intranet->cardnumber;
            $APPLICATION->status = "conditions";
            $APPLICATION->bookingno = $this->intranet->bookingno;
            $this->entry_pin->set_text('');
            $this->entry_barcode->set_text('');
             $this->button_book->set_sensitive(true);
            $APPLICATION->conditions();
          }
        elseif ($APPLICATION->intranet->booked == "offline")
        {

            global $Windows;

            $APPLICATION->status = "offline";
        
          $Windows['offline']->on();
         foreach ($Windows as $key => $window)
               {

                if ($key != "offline") {
                  $window->off();}
              }
           
        }
        else
          {
            $this->entry_pin->set_text("");
            //$this->popup("Invalid PIN");
             
             $this->label_error->set_text("             Invalid PIN");
            
           
            
          }
       }

$this->button_book->set_sensitive(true);
     }
/*
     public function popup($message)
     {
          $dialog = new GtkDialog('Error', null, Gtk::DIALOG_MODAL); // create a new dialog
                $dialog->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
                $top_area = $dialog->vbox; // note 2
                $top_area->pack_start($hbox = new GtkHBox()); // note 3
                $stock = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_WARNING,
                Gtk::ICON_SIZE_DIALOG); // note 4
                $hbox->pack_start($stock, 0, 0); // stuff in the icon


             //   $text = settype($APPLICATION->intranet->failure,"string");
               // echo "failure is $text TTTTT " . $APPLICATION->intranet->failure ."SSSSSSSSSS";
                $hbox->pack_start(new GtkLabel($message)); // and the msg
                $dialog->add_button(Gtk::STOCK_OK, Gtk::RESPONSE_OK); // note 5
                $dialog->set_has_separator(false); // don't display the set_has_separator
                $dialog->show_all(); // show the dialog
                $dialog->run(); // the dialog in action
                $dialog->destroy(); // done. close the dialog box.
               //

     }
*/

     public function on()
     {

         
    

         $this->model->clear();
         //print_r($this->intranet->times);
         if (count($this->intranet->times) > 0){

         $counter = 0;
         foreach($this->intranet->times as $choice)
            {
            $choice = round($choice / 60) . " minutes";
            $this->model->append(array($choice));
            $this->combobox->set_active($counter);
            $counter++;
            }
            
         }
        // $this->entry_barcode->grab_focus();

         $this->show_all();

         global $windowOnTop;
         $windowOnTop = $this;
       //  $this->set_keep_above(TRUE);
     }

      public function off()
     {
         $this->label_error->set_text("");
         $this->entry_pin->set_text('');
         $this->entry_barcode->set_text('');

          $this->hide_all();
         $this->set_keep_above(FALSE);
     }

}


class resetWindow2
{


    public function reset()
    {
      

     global $APPLICATION;

   $APPLICATION->check_status();


    }

}

////////////////////////// END class definitions

function checkstatus()
{
    global $APPLICATION;
   
   $APPLICATION->check_status();//
   return true;
}



function start()
{
//global $wnd;
global $APP_STATUS ;
global $timer;
global $APPLICATION;
global $INTRANET;
global $Windows;
global $Reset;

Gtk::timeout_remove($timer);
$APP_STATUS = "checking";
//$wnd->remove_mainbox();




$INTRANET = new Intranet();

$Windows['available'] = new WinAvailable($INTRANET);
$Windows['barcode'] = new WinBarcode($INTRANET);
$Windows['pin'] = new WinPin($INTRANET);
$Windows['conditions'] = new WinConditions($INTRANET);
$Windows['offline'] = new WinOffline($INTRANET);
$Windows['clock'] = new WinClock($INTRANET);
$Windows['locked'] = new WinLocked();
$Windows['next'] = new WinNext();

//$wnd->destroy();
$APPLICATION = new Application($INTRANET);

 
$Reset['loaded'] = new resetWindow2($APPLICATION);



//$checktimer = Gtk::timeout_add(10000, 'checkstatus');
}

/* 
$loadhtml = Gtk::timeout_add(1000, 'loadgtkhtml');

function loadgtkhtml()
{
global $APPLICATION;
global $CONFIG;
//$APPLICATION->Windows['conditions']->loadgtkhtml();

$html_text = @file_get_contents($CONFIG->conditions);

if ($html_text)
{
   // echo $html_text;
    $APPLICATION->html_text = $html_text;
    return false;
}
else
{
    sleep(30);
    return true;
}



}
*/

$clock = Gtk::timeout_add(1000, 'clock');

function clock()
{
global $APPLICATION;
global $INTRANET;
global $NEXTCHECK;
global $Windows;

$now = time();

if ($NEXTCHECK < $now)
{
     //echo "checking status
//";
   $APPLICATION->check_status();
}



 $file = "../data/check.txt";

if ($APPLICATION->intranet->poll_client == 1  && file_exists($file))
{
   
$data = file_get_contents($file);
 $gap = $now - $APPLICATION->last_check;

 if (trim($data) == "check" ) {echo "$gap check is check
 ";}



 if (trim($data) == "check" && ($gap > 10) )
         {
     file_put_contents($file, "none");
   //  echo "resetting to none
//";
    
     $APPLICATION->check_status();
 //exit();
     }
//echo "$data
//";
}


if ($APPLICATION->status == "clock")
    {
    
    $secondsleft = $APPLICATION->next - $now;
//echo "updating clock
//";

    if ($secondsleft < 1)
    {
      $APPLICATION->status = '';
      $APPLICATION->check_status();
        return true;
    }
   // else {}
 
    $Windows['clock']->update($secondsleft);
echo " $INTRANET->extend $secondsleft $INTRANET->warning_1 $INTRANET->warning_2
";
    if ($secondsleft == $INTRANET->warning_1)
    {
        if ($INTRANET->extend == "yes")
        {

        $INTRANET->next();

        if ($INTRANET->nexttimeslot == "yes")
            {
            $APPLICATION->show_extend($INTRANET->warning_1_text);
            }
        else
            {
            $APPLICATION->show_warning($INTRANET->warning_1_text);
            }
        }
        else
        {
         $APPLICATION->show_warning($INTRANET->warning_1_text);
        }
    }


  if ($secondsleft == $INTRANET->warning_2)
    {
        if ($INTRANET->extend == "yes")
        {

        $INTRANET->next();

        if ($INTRANET->nexttimeslot == "yes")
            {
            $APPLICATION->show_extend($INTRANET->warning_2_text);
            }
        else
            {
            $APPLICATION->show_warning($INTRANET->warning_2_text);
            }
        }
        else
        {
         $APPLICATION->show_warning($INTRANET->warning_2_text);
        }
    }


    
    }
    return true;
}