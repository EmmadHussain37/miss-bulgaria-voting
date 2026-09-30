(function($){

"use strict";


$(document).ready(function(){


let selectedCandidate = null;



function mbvRefreshLeaderboard(){


    if(
        $('#mbv-vote-modal').hasClass('active')
    ){

        return;

    }



    $.ajax({

        url: window.location.href,

        method:'GET',

        success:function(html){


            let newLeaderboard =
            $(html).find('.mbv-leaderboard').html();



            if(newLeaderboard){


                $('.mbv-leaderboard')
                .html(newLeaderboard);


            }


        }


    });


}





/*
|--------------------------------------------------------------------------
| Open Vote Modal
|--------------------------------------------------------------------------
*/


$(document).on(
'click',
'.mbv-vote-btn',
function(e){

    e.preventDefault();


    selectedCandidate = $(this);

	$('#mbv-vote-modal')
.removeClass('active');

    $('.mbv-selected-candidate')
    .text(
        selectedCandidate.data('name')
    );


    $('#mbv-vote-modal')
    .addClass('active');


});







/*
|--------------------------------------------------------------------------
| Close Vote Modal
|--------------------------------------------------------------------------
*/


$('.mbv-close-modal').on('click',function(){


    $('#mbv-vote-modal')
    .removeClass('active');


});





$(document).on(
'click',
'#mbv-vote-modal',
function(e){


    if($(e.target).is('#mbv-vote-modal')){


        $('#mbv-vote-modal')
        .removeClass('active');


    }


});








/*
|--------------------------------------------------------------------------
| Confirm Free Vote
|--------------------------------------------------------------------------
*/


$('.mbv-confirm-vote').on('click',function(){


    if(!selectedCandidate){

        return;

    }



    let button = selectedCandidate;


    let candidate =
    button.data('id');


    let modalButton =
    $(this);





    $.ajax({


        url:mbvData.api,


        method:'POST',



        data:{


            candidate_id:candidate,

            votes:1,
			
			website:''

        },



        beforeSend:function(){


            modalButton

            .addClass('loading')

            .prop('disabled',true)

            .text('Processing...');


        },



        success:function(response){


            $('#mbv-vote-modal')
            .removeClass('active');



            button

            .text('Vote Submitted ✓')

            .addClass('success');


        },



        error:function(){


            modalButton

            .removeClass('loading')

            .prop('disabled',false)

            .text('Try Again');


        }



    });



});








/*
|--------------------------------------------------------------------------
| Paid Package Checkout
|--------------------------------------------------------------------------
*/


$(document).on('click','.mbv-buy-votes',function(e){

    e.preventDefault();



    console.log(
        'BUY BUTTON CLICKED'
    );



    let button=$(this);



    let candidate =
    button.data('candidate');



    let package_id =
    button.data('package');




    if(!candidate || !package_id){


        console.error(
            'Missing candidate or package ID'
        );


        return;


    }



    $.ajax({


        url:mbvData.checkout,


        method:'POST',



        beforeSend:function(xhr){


            xhr.setRequestHeader(

                'X-WP-Nonce',

                mbvData.nonce

            );


            button

            .addClass('loading')

            .prop('disabled',true)

            .text('Redirecting...');


        },



        data:{

    candidate_id:candidate,

    package_id:package_id,

    personal_message: $('#mbv-personal-message').val()

},
		
		
		        success:function(response){


            console.log(
                'STRIPE RESPONSE',
                response
            );



            if(response.url){


                console.log(
                    'REDIRECTING TO STRIPE'
                );


                window.location.replace(
                    response.url
                );


            }


        },



        error:function(xhr){


            console.log(
                xhr.responseText
            );


            button

            .removeClass('loading')

            .prop('disabled',false)

            .text('Try Again');


        }


    });



});









/*
|--------------------------------------------------------------------------
| Payment Success Popup
|--------------------------------------------------------------------------
*/


let paymentParams =
new URLSearchParams(
    window.location.search
);



let paymentStatus =
paymentParams.get('payment');





if(paymentStatus === 'success'){


    console.log(
        'Payment success detected'
    );



    let candidateID =
    paymentParams.get('candidate_id');



   let addedVotes = 0;





    let matchedCard =
    $('.mbv-ranking-card[data-candidate-id="' + candidateID + '"]');



    let candidateName =
    'Candidate';




    if(matchedCard.length){


        candidateName =
        matchedCard.find('h3')
        .text()
        .trim();



        matchedCard
        .addClass('voted-candidate');


    }






    $('#mbv-success-name')
    .text(
        'You voted for ' + candidateName
    );



    $('#mbv-success-votes')
.text(
    'Votes added successfully'
);



    $('#mbv-success-total')
    .text(
        'Current Votes: Loading...'
    );







    if(candidateID){


        $.ajax({


            url:
            mbvData.site_url +
            '/wp-json/mbv/v1/candidate-stats',



            method:'GET',



            data:{


                candidate_id:candidateID


            },



            success:function(response){



                if(response.total_votes){


                    $('#mbv-success-total')
                    .text(
                        'Current Votes: ' +
                        response.total_votes
                    );



                    matchedCard
                    .find('.mbv-vote-count')
                    .text(
                        response.total_votes +
                        ' Votes'
                    );



                    setTimeout(function(){


                        mbvRefreshLeaderboard();


                    },1000);


                }






                if(response.rank){


                    $('#mbv-success-rank')
                    .text(
                        'Current Rank: #' +
                        response.rank
                    );


                }






                if(response.rank == 1){


                    $('#mbv-success-needed')
                    .text(
                        '🏆 You are leading the competition!'
                    );


                }
                else{


                    $('#mbv-success-needed')
                    .text(
                        'Need ' +
                        response.needed +
                        ' more votes to reach #1'
                    );


                }



            }



        });



    }







    setTimeout(function(){


        if($('#mbv-payment-success').length){


            $('#mbv-payment-success')
            .addClass('active');


            console.log(
                'Popup opened'
            );


        }


    },500);



}






jQuery(document).ready(function($){

    $('.mbv-paid-vote-btn').on('click', function(e){

        e.preventDefault();

        let candidateID = $(this).data('candidate-id');
        let candidateName = $(this).data('candidate-name');
		let candidateRegion = $(this).data('candidate-region');
		let candidateImage = $(this).data('candidate-image');
		let candidateMessage = $(this).data('candidate-message');

        $('#mbv-paid-candidate-name').text(candidateName);
		$('.mbv-region').text(candidateRegion);
		$('#mbv-paid-candidate-image').attr('src', candidateImage);
		$('#mbv-paid-candidate-message').text(
    candidateMessage || 
    'Every vote brings her closer to the Final.'
);

        $('#mbv-paid-vote-popup')
            .attr('data-candidate-id', candidateID)
            .fadeIn();

		$.post(
    mbvData.ajax_url,
    {
        action: 'mbv_load_paid_vote_packages',
        candidate_id: candidateID
    },
    function(response){

        $('#mbv-paid-vote-packages').html(response);

    }
);
    });


    $('.mbv-paid-vote-close, .mbv-paid-vote-overlay').on('click', function(){

        $('#mbv-paid-vote-popup').fadeOut();

    });

});
	
/*
|--------------------------------------------------------------------------
| Region Filters
|--------------------------------------------------------------------------
*/


$(document).on(
    'click',
    '.mbv-region-filter',
    function(){


        let region = String($(this).data('region') || '').toLowerCase().trim();



        $('.mbv-region-filter')
        .removeClass('active');


        $(this)
        .addClass('active');




        $('.mbv-card').each(function(){


            let cardRegion = String($(this).data('region') || '').toLowerCase().trim();



            if(region === 'all'){

                $(this).fadeIn();

            }

            else if(cardRegion === region){

                $(this).fadeIn();

            }

            else{

                $(this).hide();

            }


        });



    }
);	

	
	
/*
|--------------------------------------------------------------------------
| Countdown Timer
|--------------------------------------------------------------------------
*/	
jQuery(document).ready(function ($) {

    function updateMBVCountdowns() {

        let $countdowns = $('.mbv-countdown');
        if (!$countdowns.length) {
            return;
        }

        $countdowns.each(function () {

            let countdownBox = $(this);

            let endDate = countdownBox.attr('data-countdown');

            if (!endDate) {
                return;
            }

            let endTime = countdownBox.data('mbv-end-time');
            if (!endTime) {
                endTime = new Date(endDate).getTime();
                countdownBox.data('mbv-end-time', endTime);
            }

            let currentTime = Date.now();

            let distance = endTime - currentTime;

            if (distance <= 0) {

                if (!countdownBox.hasClass('mbv-ended')) {
                    countdownBox.addClass('mbv-ended').html(
                        '<span class="mbv-countdown-ended">Voting Closed</span>'
                    );
                }

                return;

            }

            let days = Math.floor(
                distance / (1000 * 60 * 60 * 24)
            );

            let hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) /
                (1000 * 60 * 60)
            );

            let minutes = Math.floor(
                (distance % (1000 * 60 * 60)) /
                (1000 * 60)
            );

            let seconds = Math.floor(
                (distance % (1000 * 60)) /
                1000
            );

            let sDays = String(days).padStart(2, '0');
            let sHours = String(hours).padStart(2, '0');
            let sMinutes = String(minutes).padStart(2, '0');
            let sSeconds = String(seconds).padStart(2, '0');

            let elDays = countdownBox.data('mbv-el-days');
            if (!elDays || !elDays.length) {
                elDays = countdownBox.find('.days');
                countdownBox.data('mbv-el-days', elDays);
            }
            if (elDays.text() !== sDays) {
                elDays.text(sDays);
            }

            let elHours = countdownBox.data('mbv-el-hours');
            if (!elHours || !elHours.length) {
                elHours = countdownBox.find('.hours');
                countdownBox.data('mbv-el-hours', elHours);
            }
            if (elHours.text() !== sHours) {
                elHours.text(sHours);
            }

            let elMin = countdownBox.data('mbv-el-min');
            if (!elMin || !elMin.length) {
                elMin = countdownBox.find('.minutes');
                countdownBox.data('mbv-el-min', elMin);
            }
            if (elMin.text() !== sMinutes) {
                elMin.text(sMinutes);
            }

            let elSec = countdownBox.data('mbv-el-sec');
            if (!elSec || !elSec.length) {
                elSec = countdownBox.find('.seconds');
                countdownBox.data('mbv-el-sec', elSec);
            }
            if (elSec.text() !== sSeconds) {
                elSec.text(sSeconds);
            }

        });

    }


    // Run immediately
    updateMBVCountdowns();


    // Update every second
    setInterval(
        updateMBVCountdowns,
        1000
    );


});
	
/*
|--------------------------------------------------------------------------
| Close Success Popup
|--------------------------------------------------------------------------
*/


$(document).on(
'click',
'.mbv-success-close',
function(){


    $('#mbv-payment-success')
    .removeClass('active');


});





});



})(jQuery);


