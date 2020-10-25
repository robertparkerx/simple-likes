jQuery( document ).ready( function ( $ ) {
	$( document ).on( 'click', '.simple-like-btn', function( e ) {
        e.preventDefault();

        var like = {
			'action' : $( this ).data( 'action' ),
			'pid'    : $( this ).data( 'pid' )
		}
		var likebtn = this;
		$.post( ajax_object.ajax_url, like ).done( function( response ) {
            response = JSON.parse( response );
			if ( response.status == 'success' ) {
				if ( response.action == 'like' ) {
                    $( likebtn ).data( 'action', 'simple-unlike' );
                    if(response.likes == 1) {
                        $( likebtn ).html( 'You like this post' );
                    } else {
                        decreasedLikes = --response.likes;
                        $( likebtn ).html( 'You and '+decreasedLikes+' other people like this post' );
                    }
					$( likebtn ).removeClass( 'like' );
					$( likebtn ).addClass( 'liked' );
				} else {
					$( likebtn ).data( 'action', 'simple-like' );
					$( likebtn ).html( 'Like' );
					$( likebtn ).removeClass( 'liked' );
					$( likebtn ).addClass( 'like' );
				}
			} else {
                console.log('error');
            }
		} );
	} );
} );