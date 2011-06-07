jQuery(document).ready(function () {

  xhr_requests = [];

  status_message_list = $(document).find('div.status ul');

  shop_ul = $('div.suggested-listings ul');

  // listen to username form submits
  $('div.main-form form').submit(function () {
    
    cancelPreviousSubmissions();
    
    username = $(this).find('input.username').val();
    
    if (!username.length)
    {
      updateStatusText('there was an issue with the name you input. please try again.');
      
      return;
    }
    
    // setup the grid plugin on the suggestions list
    vg = shop_ul.vgrid({
      easeing: "easeOutQuint",
      time: 10,
      delay: 0,
      fadeIn: {
        time: 50,
        delay: 10
      }
    });
    
    // the request data to send to the server
    var data = {
      'username': username
    }
    
    // the ajax request config
    var settings = {
      'url': 'shop_suggestions.php',
      'data': data,
      'dataType': 'json',
      'success': shopListReturned
      };
    
    // make the request
    xhr_requests.push( $.ajax(settings) );
    
    // show the loading animation
    updateStatusText('loading the favorite shops for '+username);
    
    return false;
  })
  
});

function cancelPreviousSubmissions()
{
  status_message_list.empty();
  
  // clear out previous suggestions
  shop_ul.empty();
  
  // stop all pending requests
  $.each(xhr_requests, function (indexInArray, valueOfElement) {
    valueOfElement.abort();
  });
}

// the shop list has successfully come back from the server
function shopListReturned (data, textStatus, jqXHR) {
  
  updateStatusText('shops loaded for '+username);
  
  if (data['error'])
  {
    updateShopListings();
    shop_ul.attr('style', '');
    updateStatusText('there was a server error retrieving the shop list. please try again.');
  }
  else
  {
    $.each(data['results'], function (indexInArray, valueOfElement) {
      window.setTimeout(function () {
        updateShopListings(valueOfElement['shop_id'], valueOfElement['login_name']);
      }, indexInArray * 200);
    });
  }
}

function updateShopListings (shop_id, shop_name) {
  var data = {
    'shop_id': shop_id
  }
  
  var settings = {
    'url': 'listing_suggestions.php',
    'data': data,
    'dataType': 'json',
    'success': listingListReturned
    };

  xhr_requests.push( $.ajax(settings) );
  
  updateStatusText('loading listings for '+shop_name);

  return false;
}

function listingListReturned (data, textStatus, jqXHR) {
  
  // for all the results given back, plug them into the list
  $.each(data['results'], function (indexInArray, valueOfElement) {
    
    window.setTimeout(function () {
      updateStatusText('loaded listing '+valueOfElement['title']+' from '+valueOfElement['shop_name']);
    
      $('#listingTemplate').tmpl(valueOfElement).appendTo(shop_ul);
    
      updateGridLayout();
    }, indexInArray * 200);
    
  });
}

function updateStatusText(text)
{
  status_message_list.prepend('<li>'+text+'</li>');
}

function updateGridLayout()
{
  // refresh and sort the grid
  window.setTimeout(function () {
    vg.vgrefresh();
    vg.vgsort(function(a, b){
      var _a = $(a).find('img').attr('alt');
      var _b = $(b).find('img').attr('alt');
      return (_a > _b) ? 1 : -1 ;
    }, "easeInOutExpo", 300, 0);
  }, 100);
}
