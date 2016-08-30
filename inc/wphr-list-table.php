<?php
/**
 * If the WP_List_Table class isn't automatically available to plugins, 
 * check if it's available and load it if necessary. 
 */
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new list table package that extends the core WP_List_Table class.
 * 
 */
class WP_HR_List_Table extends WP_List_Table {
    /**
     * Set up a constructor that references the parent constructor.
     */
    function __construct() {
        global $status, $page;
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'reservation',     //singular name of the listed records
            'plural'    => 'reservations',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    /**
     * Returns an array of data 
     * @return array
     */
    function data() {
        global $wpdb;
        $results = $wpdb->get_results( 'SELECT * FROM ' . WP_HR_TABLE, ARRAY_A );
        return $results;  
    }

    /**
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. 
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     */

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'name':
                return $item[ $column_name ];
            case 'email':
                return $item[ $column_name ];
            case 'phone':
                return $item[$column_name];
            case 'reserved_at':
                return $item[ $column_name ];
            case 'from_date':
                return $item[ $column_name ];
            case 'to_date':
                return $item[ $column_name ];
            case 'room_type':
                return $item[ $column_name ];
            case 'room_requirements' :
                return $item[ $column_name ];
            case 'adults':
                return $item[ $column_name ];
            case 'children':
                return $item[ $column_name ];
            case 'special_requirements' :
                return $item[ $column_name ];
            case 'status':
                return $this->reservation_status( $item[ 'id' ] );
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Uses column_$custom( $item )
     * on hover name column shows row actions
     * shows row id next to name 
     * @param array $item A singular item (one full row's worth of data)
     */
    function column_name( $item ) {
        
        // Build row actions
        $actions = array(
            'confirm' => sprintf( '<a href="?page=%s&action=%s&reservation=%s">Confirm</a>', $_REQUEST[ 'page' ], 'confirm', $item[ 'id' ] ),
            'delete' => sprintf( '<a href="?page=%s&action=%s&reservation=%s">Delete</a>', $_REQUEST[ 'page' ], 'delete', $item[ 'id' ] ),
        );
        
        //Return the title contents
        return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s', 
            /*$1%s*/ $item[ 'name' ],
            /*$2%s*/ $item[ 'id' ], 
            /*$3%s*/ $this->row_actions( $actions )
        );
    }

    /**
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the name column <td> 
     */

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args[ 'singular' ],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item[ 'id' ]                //The value of the checkbox should be the record's id
        );
    }

    /**
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information
     */
    function get_columns() {
        $columns = array(
            'cb'                   => '<input type="checkbox" />', //Render a checkbox instead of text
            'name'                 => 'Name',
            'email'                => 'Email',
            'phone'                => 'Phone',
            'reserved_at'          => 'Rserved At',
            'from_date'            => 'From Date',
            'to_date'              => 'To date',
            'room_type'            => 'Room Type',
            'room_requirements'    => 'Room Requirements',
            'adults'               => 'Adults',
            'children'             => 'Children',
            'special_requirements' => 'Special Requirements',
            'status'               => 'Status',
        );
        return $columns;
    }

    /** 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */

    function get_sortable_columns() {
        $sortable_columns = array(
            'name'               => array( 'name', false ),     //true means it's already sorted
            'email'              => array( 'email', false ),
            'reserved_at'        => array( 'reserved_at', false ),
            'from_date'          => array( 'from_date', false ),
            'to_date'            => array( 'to_date', false ),
            'room_type'          => array( 'room_type', false ),
            'room_requirements'  => array( 'room_requirements', false )
        );
        return $sortable_columns;
    }

    /** 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     */
 
    function get_bulk_actions() {
        $actions = array(
            'deletebulk'  => 'Delete',
            'confirmbulk' => 'Confirm',
        );
        return $actions;
    }

    /**
     * Handles bulk actions
     * @see $this->prepare_items()
     */
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete' === $this->current_action() ) {
            $id = $_REQUEST[ 'reservation' ];
            $status = $this->delete_item( $id );
            if( $status ) {
                echo '<div class="updated notice is-dismissible" id="message"><p>Item deleted. <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        } elseif( 'deletebulk' === $this->current_action() ) {
            $ids = $_REQUEST[ 'reservation' ];
            foreach( $ids as $id ) {
                $status = $this->deleteItem( $id );
            }
            if( $status ) {
                echo '<div class="updated notice is-dismissible" id="message"><p>Item deleted. <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        } elseif( 'confirm' === $this->current_action() ) {
            $id = $_REQUEST[ 'reservation' ];
            $status = $this->confirm_item( $id );
            if( $status ) {
                echo '<div class="updated notice is-dismissible" id="message"><p>Reservation confirmed. <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        } else if( 'confirmbulk' === $this->current_action() ) {
            $ids = $_REQUEST[ 'reservation' ];
            foreach( $ids as $id ){
                $status = $this->confirm_item( $id );
            }
            if( $status ) {
                echo '<div class="updated notice is-dismissible" id="message"><p>Reservation confirmed. <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        }
    }

    /**
     * Deletes an item
     */
    function delete_item( $id ) {
        global $wpdb;
        $status = $wpdb->delete( WP_HR_TABLE, array( 'id' => $id ) );
        return $status;
    }

    /**
     * Confirms an item
     */
    function confirm_item( $id ) {
        global $wpdb;
        $status = $wpdb->update( WP_HR_TABLE, array( 'status' => 1 ), array( 'id' => $id ) );
        return $status;
    }

    /**
     * Returns status of an item
     * @return HTML
     */
    function reservation_status( $id ) {
        global $wpdb;
        $status = $wpdb->get_var( 'SELECT status FROM '.WP_HR_TABLE.' WHERE id='.$id );
        $status = ( $status == 0 ) ? '<span class="pending">Pending</span>' : '<span class="confirmed">Confirmed</span>';
        return $status;
    }
    
    /**
     * prepare data for display.
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */

    function prepare_items() {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 10;        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();        
        $this->_column_headers = array( $columns, $hidden, $sortable );        
        $this->process_bulk_action();        
        $data = $this->data();
                
        function usort_reorder( $a,$b ) {
            $orderby = ( ! empty( $_REQUEST[ 'orderby' ] ) ) ? $_REQUEST[ 'orderby' ] : 'name'; //If no sort, default to title
            $order = ( ! empty( $_REQUEST[ 'order' ] ) ) ? $_REQUEST[ 'order' ] : 'asc'; //If no order, default to asc
            $result = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order
            return ( $order === 'asc' ) ? $result : -$result; //Send final sort direction to usort
        }
        usort( $data, 'usort_reorder' );        
        $current_page = $this->get_pagenum();
        $total_items = count( $data );        
        $data = array_slice( $data, ( ( $current_page-1 ) * $per_page ), $per_page );
        $this->items = $data;        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                    //WE have to calculate the total number of items
            'per_page'    => $per_page,                       //WE have to determine how many items to show on a page
            'total_pages' => ceil( $total_items / $per_page ) //WE have to calculate the total number of pages
        ) );
    }
}

/**
 * This function renders the reservations list table.
 * @uses items
 * @uses prepare_items()
 * @uses data()
 * @uses display()
 * @return HTML
 */
function render_list_table() {
    
    //Create an instance of our package class...
    $listTable = new WP_HR_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $listTable->prepare_items();
    $listTable->data();

    ?>
    <div class="wrap">        
        <div id="icon-users" class="icon32"><br /></div>
        <form method="post">
          <input type="hidden" name="page" value="my_list_test" />
          <?php $listTable->search_box( 'search', 'search_id' ); ?>
        </form>        
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="booking-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST[ 'page' ] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $listTable->display(); ?>
        </form>        
    </div>
    <?php
}