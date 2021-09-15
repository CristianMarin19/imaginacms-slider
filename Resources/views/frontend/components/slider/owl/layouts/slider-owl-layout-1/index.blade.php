<x-isite::edit-link link="/iadmin/#/slider/index?edit={{$slider->id}}"
                    :tooltip="trans('slider::slider.editLink.tooltipSlider')"/>
<div id="{{ $slider->system_name }}"
     class="owl-carousel slider-component owl-theme owl-slider-layout-1{{ $dots ? ' owl-with-dots carousel-indicators-position-'.$dotsPosition.' carousel-indicators-style-'. $dotsStyle: '' }}"
     style="max-height: {{ $height }}">
  @foreach($slider->slides as $index => $slide)

    @switch($slide->type)

      @case("video")
      <div class="item h-100">
        @if($slide->mediaFiles()->slideimage->isVideo)
          <video class="d-block h-100 slider-img__{{$imgObjectFit}}" width="100%" loop autoplay muted>
            <source src="{{ $slide->mediaFiles()->slideimage->path }}"/>
          </video>
        @else
          <iframe class="full-height" width="100%" height="{{$height}}" src="{{ $slide->getLinkUrl() }}"
                  frameborder="0" allowfullscreen></iframe>
        @endif
      </div>
      @break
      @default
      <div class="item h-100">
        @if($slide->mediaFiles()->slideimage->isVideo)
          <video class="d-block h-100 slider-img__{{$imgObjectFit}}" width="100%" loop autoplay muted>
            <source src="{{ $slide->mediaFiles()->slideimage->path }}"/>
          </video>
        @elseif($slide->mediaFiles()->slideimage->isImage)
          <x-media::single-image :alt="$slide->title ?? Setting::get('core::site-name')"
                                 :title="$slide->title ?? Setting::get('core::site-name')"
                                 :url="$slide->uri ?? $slide->url ?? null" :isMedia="true"
                                 imgClasses="d-block h-100 slider-img__{{$imgObjectFit}}"
                                 width="100%"
                                 :mediaFiles="$slide->mediaFiles()" zone="slideimage"/>

        @endif
        @if(!empty($slide->title) || !empty($slide->caption) || !empty($slide->custom_html))
          <div class="carousel-caption px-o pb-0 d-none d-md-block h-100">
            <div class="container h-100">
              <div class="row h-100 justify-content-center">
                <div class="col-10 text-center">

                  @if(!empty($slide->title))
                    <h1 class="title1 mb-2 h1"><b>{{$slide->title}}</b></h1>
                  @endif

                  @if(!empty($slide->custom_html))
                    <div class="custom-html d-none d-md-block">
                      <a href="{{ $slide->url ?? $slide->uri }}">
                        {!! $slide->custom_html !!}
                      </a>
                    </div>
                  @endif

                  <div class="d-block">
                    <a class="btn btn-primary"
                       href="{{ $slide->url ?? $slide->uri }}">{{ $slide->caption ?? trans('isite::common.menu.viewMore') }}</a>
                  </div>

                </div>
              </div>
            </div>
          </div>
        @endif
      </div>
      @break
    @endswitch
  @endforeach
</div>
@section('scripts-owl')
  @parent
  <script>
    $(document).ready(function () {
      $('#{{ $slider->system_name }}').owlCarousel({
        items: 1,
        dots: {!! $dots ? 'true' : 'false' !!},
        loop: {!! $loop ? 'true' : 'false' !!},
        lazyLoad: true,
        margin: {!! $margin !!},
        nav: {!! $nav ? 'true' : 'false' !!},
        autoplay: {!! $autoplay ? 'true' : 'false' !!},
        autoplayHoverPause: {!! $autoplayHoverPause ? 'true' : 'false' !!},
        responsiveClass: {!! $responsiveClass ? 'true' : 'false' !!},
        responsive: {!! $responsive!!},
        autoplayTimeout: {{$autoplayTimeout}},
        {!! !empty($navText) ? 'navText: '.$navText."," : "" !!}
      });
    });
  </script>
@stop