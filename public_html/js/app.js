(function($) {


  $(".toggle-resule").click(function() {
      $(this).parent().prev().toggle();
  });

    $("#delete").click(function() {
    var ids="";
    var r = confirm("Do you really want to delete selected Status");
    if (r == true) {
      $("input[name='todelete[]']").each(function (index, obj) {
        if (this.checked) {
          ids+="_"+$(this).val();
        }
      });
      var data_my = "ids="+ids;

      $.ajax({
            type: 'POST',
            url: "/admin/status/multidelete.html",
            data: data_my,
            success: function(resultData) { 
              alert(resultData)
              location.reload();
              },
            error: function() { alert("Something went wrong"); }
      });
    }
  });
  $("#approve").click(function() {
      var ids="";
      var r = confirm("Do you really want to approve selected Status");
      if (r == true) {
        $("input[name='todelete[]']").each(function (index, obj) {
          if (this.checked) {
            ids+="_"+$(this).val();
          }
        });
        var data_my = "ids="+ids;

        $.ajax({
              type: 'POST',
              url: "/admin/status/multireview.html",
              data: data_my,
              success: function(resultData) { 
                alert(resultData)
                location.reload();
                },
              error: function() { alert("Something went wrong"); }
        });
      }
  });

  var themovieddUrl = "https://api.themoviedb.org/3/";

  var postersList;
  $("#search-imdb-tv").on("click",function() {
    var value = $("#input-imdb").val();
    var type = $("#type-imdb").val();
    if (type == "title") {

      var d = {
        "api_key": themoviedb_key,
        "query": value,
        "language": language
      };
      $.ajax({
        data: d,
        url: themovieddUrl + "search/tv",
        success: function(result) {
          if (result.total_results == 0) {
            alert("No Movies Founded");
          } else {
            var List = "";
            postersList = new Array();
            for (var i = 0; i < result.results.length; i++) {
              postersList[i] = result.results[i];
              List += "<div id='" + i + "'  alt=" + result.results[i].id + " class='poster-search select_serie_tv'><img src='https://image.tmdb.org/t/p/w400" + result.results[i].poster_path + "'/><span>" + result.results[i].original_name + "</span><div>" + result.results[i].first_air_date + "</div></div>";
            }
            $("#result_search").html(List);
            $("#div1").show();
          }
        }
      });
    } else {
      var d = {
        "api_key": themoviedb_key,
        "language": language,
        "external_source": "imdb_id"
      };
      $.ajax({
        data: d,
        url: themovieddUrl + "find/" + value,
        success: function(result) {
          if (result.tv_results.length == 0) {
            alert("No Movies Founded");
          } else {
            var List = "";
            postersList = new Array();
            for (var i = 0; i < result.tv_results.length; i++) {
              postersList[i] = result.tv_results[i];
              List += "<div  id='" + i + "'  alt=" + result.tv_results[i].id + " class='poster-search select_serie_tv'><img src='https://image.tmdb.org/t/p/w400" + result.tv_results[i].poster_path + "'/><span>" + result.tv_results[i].original_name + "</span><div>" + result.tv_results[i].first_air_date + "</div></div>";
            }
            $("#result_search").html(List);
            $("#div1").show();
          }
        }
      });
    }
  });




  $("#close_search").on("click",function() {
    $("#div1").hide();
  })
  $(".btn-select").on("click",function() {
    $(".input-file").click();
  })


  $(".select-video").on("click",function() {
    $(".input-video").click();
  })
  $("#Video_color").change(function() {
    $(".textarea-quotes").css("background-color", "#" + $(this).val());
    $(".quote-view").css("background-color", "#" + $(this).val());
  })

  $(".input-video").change(function(evt) {
    var $source = $('#video_here');
    $source[0].src = URL.createObjectURL(this.files[0]);
    $source.parent()[0].load();
    $source.parent("video").css({
      "display": "block"
    })
  });
  $(".img-selector").change(function() {
    readURL(this, "#img-preview");
  });
  $(".alert-dashborad .close").on("click",function() {
    $(".alert-dashborad").fadeOut();
  })
  $("#new_season_btn").on("click",function() {
    $("#new_season_dialog").show();
    return false;

  })
  $("#new_season_btn_close").on("click",function() {
    $("#new_season_dialog").hide();
    return false;
  })
  $("#movie_sourcetype").change(function(argument) {
    if ($("#movie_sourcetype").val() == 5) {
      $("#movie_sourceurl").parent().hide();
      $("#movie_sourcefile").parent().show();
    } else {
      $("#movie_sourceurl").parent().show();
      $("#movie_sourcefile").parent().hide();
    }
  });
  $("#source_type").change(function(argument) {
    if ($("#source_type").val() == 5) {
      $("#source_url").parent().hide();
      $("#source_file").parent().show();
    } else {
      $("#source_url").parent().show();
      $("#source_file").parent().hide();
    }
  });
  $("#movie_trailertype").change(function(argument) {
    if ($("#movie_trailertype").val() == 5) {
      $("#movie_trailerurl").parent().hide();
      $("#movie_trailerfile").parent().show();
    } else {
      $("#movie_trailerurl").parent().show();
      $("#movie_trailerfile").parent().hide();
    }
  });
  $("#serie_trailertype").change(function(argument) {
    if ($("#serie_trailertype").val() == 5) {
      $("#serie_trailerurl").parent().hide();
      $("#serie_trailerfile").parent().show();
    } else {
      $("#serie_trailerurl").parent().show();
      $("#serie_trailerfile").parent().hide();
    }
  });


  $(".btn-select-1").on("click",function() {
    $(".input-file-1").click();
  })
  $(".img-selector-1").change(function() {
    readURLL(this, "#img-preview-1");
  });
  $(".btn-select-2").on("click",function() {
    $(".input-file-2").click();
  })
  $(".img-selector-2").change(function() {
    readURLL(this, "#img-preview-2");
  });
  $(".btn-select-3").on("click",function() {
    $(".input-file-3").click();
  })
  $(".img-selector-3").change(function() {
    readURLL(this, "#img-preview-3");
  });
  $(".input-btn-3").on("click",function() {
    $(".input-file-3").click();
  })

  function readURL(input, pr) {
    if (input.files && input.files[0]) {
      var countFiles = input.files.length;
      var imgPath = input.value;
      var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

      if (extn == "png" || extn == "jpg" || extn == "jpeg") {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(pr).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      } else {
        alert("the image file is not valid please select a valid image file : png,jpg or jpeg");
      }
    }
  }



  var imagesPreview = function(input, placeToInsertImagePreview) {

    if (input.files) {
      var filesAmount = input.files.length;

      for (i = 0; i < filesAmount; i++) {
        var reader = new FileReader();

        reader.onload = function(event) {
          $($.parseHTML('<img style="width:210px;height:auto;display:inline-block;margin:15px"  class="thumbnail thumbnail-2" />')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
        }

        reader.readAsDataURL(input.files[i]);
      }
    }

  };

  $('#product_files').on('change', function() {
    imagesPreview(this, 'div.gallery');
  });



  $(".img-thum-product").on("click",function() {
    $("#image_product_view").attr({
      "src": $(this).attr("data")
    });
    $(".img-thum-product").removeClass("img-thum-product-active");
    $(this).addClass("img-thum-product-active");
  });
  $(".button-file").change(function() {
    readURL(this);
  });

  function readURL(input) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('.image-preview').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }



  // Multiple images preview in browser
  var imagesPreview = function(input, placeToInsertImagePreview) {

    if (input.files) {
      var filesAmount = input.files.length;

      for (i = 0; i < filesAmount; i++) {
        var reader = new FileReader();

        reader.onload = function(event) {
          $($.parseHTML('<img style="width:230px;height:230px;margin:10px;display:inline-block"  class="thumbnail thumbnail-2" >')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
        }

        reader.readAsDataURL(input.files[i]);
      }
    }

  };
  $('#Wallpaper_files').on('change', function() {
    $("div.gallery").html("");
    imagesPreview(this, 'div.gallery');
  });



  $(".img-selector").change(function() {
    readURLL(this, "#img-preview");
  });

  function readURLL(input, pr) {
    if (input.files && input.files[0]) {
      var countFiles = input.files.length;
      var imgPath = input.value;
      var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

      if (extn == "png" || extn == "jpg" || extn == "jpeg" || extn == "gif") {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(pr).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      } else {
        alert("the image file is not valid please select a valid image file : png,jpg or jpeg");
      }
    }
  }
})(jQuery);