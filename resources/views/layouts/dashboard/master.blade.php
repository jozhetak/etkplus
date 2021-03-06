<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>@yield('title')</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
     <!-- Bootstrap core CSS     -->
    <link href="/assets/dashboard/css/bootstrap.min.css" rel="stylesheet" />

    <!--  Paper Dashboard core CSS    -->

    <link href="/assets/dashboard/css/paper-dashboard.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/dashboard/css/app.css">
    <link href="/assets/css/photoswipe.css" rel="stylesheet" />
    <link href="/assets/css/default-skin.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/dashboard/css/materialize.css">


    <!--  Fonts and icons     -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
</head>

<body>
   @yield('content')
</body>

    <!--   Core JS Files. Extra: TouchPunch for touch library inside jquery-ui.min.js   -->
    <script src="/assets/dashboard/js/jquery-3.1.1.min.js" type="text/javascript"></script>
    <script src="/assets/dashboard/js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="/assets/dashboard/js/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="/assets/dashboard/js/bootstrap.min.js" type="text/javascript"></script>

    <!--  Forms Validations Plugin -->
    <script src="/assets/dashboard/js/jquery.validate.min.js"></script>

    <!-- Promise Library for SweetAlert2 working on IE -->
    <script src="/assets/dashboard/js/es6-promise-auto.min.js"></script>

    <!--  Plugin for Date Time Picker and Full Calendar Plugin-->
    <script src="/assets/dashboard/js/moment.min.js"></script>

    <!--  Date Time Picker Plugin is included in this js file -->
    <script src="/assets/dashboard/js/bootstrap-datetimepicker.js"></script>

    <!--  Select Picker Plugin -->
    <script src="/assets/dashboard/js/bootstrap-selectpicker.js"></script>

    <!--  Switch and Tags Input Plugins -->
    <script src="/assets/dashboard/js/bootstrap-switch-tags.js"></script>

    <!-- Circle Percentage-chart -->
    <script src="/assets/dashboard/js/jquery.easypiechart.min.js"></script>

    <!--  Charts Plugin -->
    <script src="/assets/dashboard/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="/assets/dashboard/js/bootstrap-notify.js"></script>

    <!-- Sweet Alert 2 plugin -->
    <script src="/assets/dashboard/js/sweetalert2.js"></script>

    <!-- Vector Map plugin -->
    <script src="/assets/dashboard/js/jquery-jvectormap.js"></script>



    <!-- Wizard Plugin    -->
    <script src="/assets/dashboard/js/jquery.bootstrap.wizard.min.js"></script>

    <!--  Bootstrap Table Plugin    -->
    <script src="/assets/dashboard/js/bootstrap-table.js"></script>

    <!--  Plugin for DataTables.net  -->
    <script src="/assets/dashboard/js/jquery.datatables.js"></script>

    <!--  Full Calendar Plugin    -->
    <script src="/assets/dashboard/js/fullcalendar.min.js"></script>

    <!-- Paper Dashboard PRO Core javascript and methods for Demo purpose -->
    <script src="/assets/dashboard/js/paper-dashboard.js"></script>


    <!-- Paper Dashboard PRO DEMO methods, don't include it in your project! -->
    <script src="/assets/dashboard/js/demo.js"></script>

    <script src="/assets/js/jasny-bootstrap.min.js"></script>
    <!--  Photoswipe files -->
<script src="/assets/js/photoswipe.min.js"></script>
<script src="/assets/js/photoswipe-ui-default.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            demo.initOverviewDashboard();
            demo.initCirclePercentage();
            demo.initFormExtendedDatetimepickers();
            demo.initWizard();
        });
    </script>
    <script>
        $('.toggle-activate-partner').on('click',function(){
            var partnerId = $(this).data('id');
            console.log(partnerId);
            console.log(1);
            });
    </script>
    <script>
        $(document).ready(function(){
           var initPhotoSwipeFromDOM = function(gallerySelector) {

    // parse slide data (url, title, size ...) from DOM elements 
    // (children of gallerySelector)
    var parseThumbnailElements = function(el) {
        var thumbElements = el.childNodes,
            numNodes = thumbElements.length,
            items = [],
            figureEl,
            linkEl,
            size,
            item;

        for(var i = 0; i < numNodes; i++) {

            figureEl = thumbElements[i]; // <figure> element

            // include only element nodes 
            if(figureEl.nodeType !== 1) {
                continue;
            }

            linkEl = figureEl.children[0]; // <a> element

            size = linkEl.getAttribute('data-size').split('x');

            // create slide object
            item = {
                src: linkEl.getAttribute('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            };



            if(figureEl.children.length > 1) {
                // <figcaption> content
                item.title = figureEl.children[1].innerHTML; 
            }

            if(linkEl.children.length > 0) {
                // <img> thumbnail element, retrieving thumbnail url
                item.msrc = linkEl.children[0].getAttribute('src');
            } 

            item.el = figureEl; // save link to element for getThumbBoundsFn
            items.push(item);
        }

        return items;
    };

    // find nearest parent element
    var closest = function closest(el, fn) {
        return el && ( fn(el) ? el : closest(el.parentNode, fn) );
    };

    // triggers when user clicks on thumbnail
    var onThumbnailsClick = function(e) {
        e = e || window.event;
        e.preventDefault ? e.preventDefault() : e.returnValue = false;

        var eTarget = e.target || e.srcElement;

        // find root element of slide
        var clickedListItem = closest(eTarget, function(el) {
            return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
        });

        if(!clickedListItem) {
            return;
        }

        // find index of clicked item by looping through all child nodes
        // alternatively, you may define index via data- attribute
        var clickedGallery = clickedListItem.parentNode,
            childNodes = clickedListItem.parentNode.childNodes,
            numChildNodes = childNodes.length,
            nodeIndex = 0,
            index;

        for (var i = 0; i < numChildNodes; i++) {
            if(childNodes[i].nodeType !== 1) { 
                continue; 
            }

            if(childNodes[i] === clickedListItem) {
                index = nodeIndex;
                break;
            }
            nodeIndex++;
        }



        if(index >= 0) {
            // open PhotoSwipe if valid index found
            openPhotoSwipe( index, clickedGallery );
        }
        return false;
    };

    // parse picture index and gallery index from URL (#&pid=1&gid=2)
    var photoswipeParseHash = function() {
        var hash = window.location.hash.substring(1),
        params = {};

        if(hash.length < 5) {
            return params;
        }

        var vars = hash.split('&');
        for (var i = 0; i < vars.length; i++) {
            if(!vars[i]) {
                continue;
            }
            var pair = vars[i].split('=');  
            if(pair.length < 2) {
                continue;
            }           
            params[pair[0]] = pair[1];
        }

        if(params.gid) {
            params.gid = parseInt(params.gid, 10);
        }

        return params;
    };

    var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
        var pswpElement = document.querySelectorAll('.pswp')[0],
            gallery,
            options,
            items;

        items = parseThumbnailElements(galleryElement);

        // define options (if needed)
        options = {

            // define gallery index (for URL)
            galleryUID: galleryElement.getAttribute('data-pswp-uid'),

            getThumbBoundsFn: function(index) {
                // See Options -> getThumbBoundsFn section of documentation for more info
                var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                    pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                    rect = thumbnail.getBoundingClientRect(); 

                return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
            }

        };

        // PhotoSwipe opened from URL
        if(fromURL) {
            if(options.galleryPIDs) {
                // parse real index when custom PIDs are used 
                // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                for(var j = 0; j < items.length; j++) {
                    if(items[j].pid == index) {
                        options.index = j;
                        break;
                    }
                }
            } else {
                // in URL indexes start from 1
                options.index = parseInt(index, 10) - 1;
            }
        } else {
            options.index = parseInt(index, 10);
        }

        // exit if index not found
        if( isNaN(options.index) ) {
            return;
        }

        if(disableAnimation) {
            options.showAnimationDuration = 0;
        }

        // Pass data to PhotoSwipe and initialize it
        gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
    };

    // loop through all gallery elements and bind events
    var galleryElements = document.querySelectorAll( gallerySelector );

    for(var i = 0, l = galleryElements.length; i < l; i++) {
        galleryElements[i].setAttribute('data-pswp-uid', i+1);
        galleryElements[i].onclick = onThumbnailsClick;
    }

    // Parse URL and open gallery if it contains #&pid=3&gid=1
    var hashData = photoswipeParseHash();
    if(hashData.pid && hashData.gid) {
        openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
    }
};

// execute above function
initPhotoSwipeFromDOM('.my-gallery');
        });
    </script>
    <script>
        $(document).ready(function(){
            $('#co-card-search-result').hide();
        });
    </script>
    <script>
        $('#co-find-card').on('click',function(){
            if ($('#co-card-input-number').val().length > 8){

                /**
                 * LOADER
                 */
                
                /**
                 * REQUEST
                 */
                $.ajax({
                    method: 'POST',
                    url: checkCardUrl,
                    data: {
                        card_number: $('#co-card-input-number').val(),
                        partner_id: partnerId,
                        _token: token
                    }
                })
                .done(function(msg){
                    console.log(JSON.stringify(msg));
                    if (msg['message'] == 'success'){
                        $('#co-card-search').hide(500);
                        $('#co-card-search-result').show(500);
                        $('#top-progress').replaceWith('<div class=\"progress\" id=\"top-progress\"></div>');
                        $('#co-search-status').addClass('has-success');
                        $('#co-search-status').removeClass('has-error');

                        $('#co-card-number').replaceWith("<h6 class=\"card-category \" id=\"co-card-number\"><span class=\"pull-left\">Номер: </span><span class=\"upper-text\">" + msg['card'].num + "</span></h6>");
                        $('#co-bonuses').replaceWith("<h6 class=\"card-category \" id=\"co-bonuses\"><span class=\"pull-left\">Бонусы на карте: </span><span class=\"upper-text\">" + msg['user_bonuses'] + "</span></h6>");
                        $('#co-operations-count').replaceWith("<h6 class=\"card-category \" id=\"co-operations-count\"><span class=\"pull-left\">Количество посещений: </span><span class=\"upper-text\">" + msg['visit_count'] + "</span></h6>");
                        $('#co-operations-summary').replaceWith("<h6 class=\"card-category \" id=\"co-operations-summary\"><span class=\"pull-left\">Сумма посещений: </span><span class=\"upper-text\">" + msg['visit_summary'] + "</span></h6>");

                        $('#co-card-number-input').val($('#co-card-input-number').val());
                    } else if (msg['message'] == 'error'){
                        $('#co-search-status').removeClass('has-success');
                        $('#co-search-status').addClass('has-error');
                    }
                    $('#co-card-info-loader').replaceWith('<i id="co-card-info-loader"></i>');
                    $('#co-max-bonuses').replaceWith('<b id="co-max-bonuses">' + msg['user_bonuses'] + '</b>');
                    $('#co-create-operation-loader').replaceWith('<i id="co-create-operation-loader"></i>');
                });

            } else {

            }
        });
    </script>
    <script>
        $('.co-form-summary').on('keyup', function(){
            var bill = $('#co-form-bill').val();
            var discount = $('#co-form-discount').val();
            var bonuses = $('#co-form-bonuses').val();
            var billWithDiscount = (bill - (bill*(discount/100)) - bonuses);
            if (!isNaN(billWithDiscount)){
              var billHtml = '<b id=\"co-bill-with-discount\">' + billWithDiscount + '</b>';
              $('#co-bill-with-discount').replaceWith(billHtml);
            }
        });
    </script>
    <script>
        $('#search-partner-list').on('keyup',function(){
            $.ajax({
                method: 'POST',
                url: searchPartnerListUrl,
                data: {
                    searchString: $('#search-partner-list').val(),
                    _token: token
                }
            })
            .done(function(msg){
                console.log(JSON.stringify(msg));
                $('#partner-list-results').replaceWith('<div id="partner-list-results"></div>');
                var searchHtml = '<div id="partner-list-results"><div class="card"><div class="card-header"><h4 class="card-title">Предприятия</h4></div><div class="card-content table-responsive table-full-width"><table class="table table-striped"><tbody>';
                searchHtml += '<thead><th>#</th><th>Название</th><th>№ договора</th></thead>';
                for (var i = 0; i <= msg['results'].length - 1; i++){
                    searchHtml += '<tr>';
                    searchHtml += '<td>' + msg.results[i].id + '</td>';
                    searchHtml += '<td><a href="/dashboard/partner/' + msg.results[i].id + '/show">' + msg.results[i].name + '</a></td>';
                    searchHtml += '<td>' + msg.results[i].contract_id + '</td>';
                    searchHtml += '</tr>';
                }
                searchHtml += '</tbody></table></div></div></div>';

                $('#partner-list-results').replaceWith(searchHtml);
            });
        });
    </script>

    <script>
        $('#cp-contract-id').on('keyup',function(){
            if($('#cp-contract-id').val().length == 5){
              $.ajax({
                  method: 'POST',
                  url: searchContractIdUrl,
                  data: {
                      searchString: $('#cp-contract-id').val(),
                      _token: token
                  }
              })
              .done(function(msg){
                
              });
            }
        });
    </script>


    <script>
        $('#ap-category').change(function(){
            $('#ap-category option:selected').each(function(){

            });
        });
    </script>
    <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter45472806 = new Ya.Metrika({
                    id:45472806,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
  <script>
      $('.away-link').on('click',function(){
        $('#top-progress').replaceWith('<div class=\"progress\" id=\"top-progress\"><div class=\"indeterminate\"></div></div>');
      });
  </script>

<script>
    $('#add-address-item').on('shown.bs.modal',function(){
        console.log('init');
        initMap();
    });
</script>
<script>
var marker;

function initMap() {
  var styleArray = [
    {
      featureType: 'all',
      stylers: [
      { saturation: -80 }
      ]
    },{
      featureType: 'road.arterial',
      elementType: 'geometry',
      stylers: [
      { hue: '#00ffee' },
      { saturation: 50 }
      ]
    },{
      featureType: 'poi.business',
      elementType: 'labels',
      stylers: [
      { visibility: 'off' }
      ]
    }
    ];
  var latlng = new google.maps.LatLng(56.123237, 47.253127);
  var map = new google.maps.Map(document.getElementById('addAddressMap'), {
    zoom: 10,
    mapTypeControl: false,
    styles: styleArray,
    center: latlng
  });

  map.addListener('click', function(e) {
    google.maps.event.trigger(map, "resize");
    placeMarkerAndPanTo(e.latLng, map);
  });
}

function placeMarkerAndPanTo(latLng, map) {
    if (marker) {
        marker.setPosition(latLng);
    } else {
     marker = new google.maps.Marker({
        position: latLng,
        map: map,
      });
    } 
    document.getElementById('new-address-latitude').value = marker.getPosition().lat();
    document.getElementById('new-address-longitude').value = marker.getPosition().lng();
}

</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBc-l0ALdzgKDwDs_qll1CKLUlEsRq5aUE&callback=initMap"></script>
@isset($partner)
<script>
    $('#tag-input').on('itemAdded',function(event){
        console.log('add');
        console.log($('#tag-input').tagsinput('items'));
        /**
         * AJAX ADDING
         */
        $.ajax({
            method: 'POST',
            url: addTagUrl,
            data: {
                partner_id: {{ $partner->id }},
                text: event.item,
                _token: token
            }
        })
        .done(function(msg){
            console.log(JSON.stringify(msg));
        });
    });
    $('#tag-input').on('itemRemoved',function(event){
        console.log('delete');
        console.log($('#tag-input').tagsinput('items'));
        /**
         * AJAX ADDING
         */
        $.ajax({
            method: 'POST',
            url: deleteTagUrl,
            data: {
                partner_id: {{ $partner->id }},
                text: event.item,
                _token: token
            }
        })
        .done(function(msg){
            console.log(JSON.stringify(msg));
        });
    });
</script>
@endisset
<noscript><div><img src="https://mc.yandex.ru/watch/45472806" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</html>