<?php
/**
 * Plugin Name: FC-Listing
 * Description: Used for FullContact contact manage
 */
class FCListing{
	/*
	*	This class describes the functionalities of FC listing plugin
	*/
	function __construct(){
		$this->init();
	}

	public function init(){
		add_action( 'init', array($this, 'fc_plugin_init') );
		add_action('wp_head', array($this, 'fc_ajaxurl'));
		add_shortcode('fc-list', array($this, 'fclist_shortcode'));
		wp_enqueue_style('bootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
		wp_enqueue_style('fc-styles', plugin_dir_url(__FILE__)."assets/css/style.css");
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'boot3','https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array( 'jquery' ) );
	}
	

public function fc_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
	
	public function fc_plugin_init(){
    $args = array(
        'public'    => true,
        'label'     => __( 'Contacts', 'textdomain' ),
        'menu_icon' => 'dashicons-book',
    );
    register_post_type( 'contact', $args );
	}
	
	public function fclist_shortcode()
        {
            $this->save_contact();
            $all_contacts = $this->get_contacts();
           ob_start();
           ?>
           <div class="contacts">
               <!-- Tab links -->
                <div class="tab">
                  <button class="tablinks" onclick="openTab(event, 'Add')">Add new contact</button>
                  <button class="tablinks" onclick="openTab(event, 'All')" id="defaultOpen">All contacts</button>
                </div>
                
                <!-- Tab content -->
                <div id="Add" class="tabcontent">
                  <h3>Add new contact</h3>
                  <form method="post">
                      <div class="row">
                          <div class="col-md-4">
                              FIrst Name:
                          </div>
                          <div class="col-md-8">
                              <input type="text" name="personFirstName">
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-4">
                              Last Name:
                          </div>
                          <div class="col-md-8">
                              <input type="text" name="personLastName">
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-4">
                              Email:
                          </div>
                          <div class="col-md-8">
                              <input type="text" name="personEmail">
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-4">
                              Phone number:
                          </div>
                          <div class="col-md-8">
                              <input type="text" name="phoneNumber">
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-12">
                              <input type="submit" value="Save" name="fc_contact_save" class="submit">
                          </div>
                      </div>
                  </form>
                </div>
                
                <div id="All" class="tabcontent all-contacts">
                  <h3>All contacts</h3>
                  <?php
                  if(isset($_POST['fc_search'])){
                  ?>
                    <div>
                        Search Results of: <?php echo $_POST['fc_search_key']; ?>
                    </div>
                    <?php } ?>
                    <div class="search-container">
                        <form method="post">
                          <input type="text" placeholder="Search.." name="fc_search_key">
                          <button type="submit" name="fc_search"><i class="fa fa-search"></i></button>
                        </form>
                      </div>
                  <table>
                      <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Description</th>
                          <th>Organization</th>
                          <th>Title</th>
                          <th>Avatar</th>
                          <th>Action</th>
                      </tr>
                      <?php
                      foreach($all_contacts as $contact){
                      ?>
                      <tr>
                          <td><?php echo get_post_meta($contact->ID, 'first_name', true)." ". get_post_meta($contact->ID, 'last_name', true) ?></td>
                          <td><?php echo get_post_meta($contact->ID, 'email', true) ?></td>
                          <td><?php echo get_post_meta($contact->ID, 'phone', true) ?></td>
                          <td><?php echo get_post_meta($contact->ID, 'description', true) ?></td>
                          <td><?php echo get_post_meta($contact->ID, 'organization', true) ?></td>
                          <td><?php echo get_post_meta($contact->ID, 'title', true) ?></td>
                          <td>
                              <?php if($avatar = get_post_meta($contact->ID, 'avatar', true)){ ?>
                              <div style="width: 70px;">
                                  <img src="<?php echo $avatar ?>">
                              </div>
                              <?php } ?>
                          </td>
                          <td style="text-align:center;">
                              <a class="fc_delete" data-id='<?php echo $contact->ID ?>'><i class="fa fa-trash-o"></i></a>
                          </td>
                      </tr>
                      <?php
                      }
                      ?>
                  </table>
                </div>
           </div>
           <script>
               function openTab(evt, cityName) {
                  var i, tabcontent, tablinks;
                  tabcontent = document.getElementsByClassName("tabcontent");
                  for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                  }
                  tablinks = document.getElementsByClassName("tablinks");
                  for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                  }
                  document.getElementById(cityName).style.display = "block";
                  evt.currentTarget.className += " active";
                }
                document.getElementById("defaultOpen").click();
                
                jQuery(".fc_delete").click(function(event){
                    event.preventDefault();
                    
                   var data = {
            		'action': 'fc_delete_contact',
            		'id': jQuery(this).data("id")
            	};
            	if (confirm('Are you sure to want you to delete this contact?')) {
                    jQuery.post(ajaxurl, data, function(response) {
            		    if(response == '1')
            		        location.href = window.location.href;
            	    });
                }
            	
                });
           </script>
           <?php
            return ob_getclean();
        }
        
    public function save_contact(){
        if(isset($_POST['fc_contact_save'])){
            
            $first_name = $_POST['personFirstName'];
            $last_name = $_POST['personLastName'];
            $email = $_POST['personEmail'];
            $phone = $_POST['phoneNumber'];
            if($email != "")
                $fc_data = $this->get_fc_api($email);
            $description = $_POST['description'];
            $organization = $_POST['organization'];
            $title = $_POST['title'];
            // Create post object
            $my_post = array(
              'post_title'    => $first_name. ' '. $last_name,
              'post_type'  => 'contact',
              'post_status'    => 'publish'
            );
             
            // Insert the post into the database
            $post_id = wp_insert_post( $my_post );
            if($post_id){
                update_post_meta($post_id, 'first_name', $first_name);
                update_post_meta($post_id, 'last_name', $last_name);
                update_post_meta($post_id, 'email', $email);
                update_post_meta($post_id, 'phone', $phone);
                if($fc_data){
                    update_post_meta($post_id, 'description', $fc_data->bio);
                    update_post_meta($post_id, 'organization', $fc_data->organization);
                    update_post_meta($post_id, 'title', $fc_data->title);
                    update_post_meta($post_id, 'avatar', $fc_data->avatar);
                }
                
            }
            return;
         
        }
        
        
    }
    public function get_fc_api($email){
        $token = 'jpbMoQIu0mH5OFwm3go2Ti2FG6vmy60S';
       header('Content-Type: application/json');
       $ch = curl_init('https://api.fullcontact.com/v3/person.enrich ');
       $post = json_encode(array('email'=>$email));
       $authorization = "Authorization: Bearer ".$token;
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
       $result = curl_exec($ch);
       curl_close($ch);
       return json_decode($result);
       
    }
    public function get_contacts(){
        $args=array(
            'post_type'      => 'contact',
            'post_status'    => 'publish',
            'posts_per_page' => -1
        );
        if(isset($_POST['fc_search'])){
            $search_key = $_POST['fc_search_key'];
            $args['meta_query'] = array(
                                    'relation' => 'OR',
                                    array(
                                        'key'     => 'first_name',
                                        'value'   => $search_key,
                                        'compare' => 'LIKE'
                                    ),
                                    array(
                                        'key'     => 'last_name',
                                        'value'   => $search_key,
                                        'compare' => 'LIKE'
                                    )
                                );
        }
        $contacts = get_posts( $args );
        return $contacts;
    }

}
require_once('inc/ajax.php');
new FCListing();