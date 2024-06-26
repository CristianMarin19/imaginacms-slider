<?php

namespace Modules\Slider\Presenters;

use Illuminate\Support\Facades\View;
use Modules\Slider\Entities\Slider;

class SliderPresenter extends AbstractSliderPresenter implements SliderPresenterInterface
{
    /**
     * renders slider.
     *
     * @param  string|Slider  $slider
     * pass Slider instance to render specific slider
     * pass string to automatically retrieve slider from repository
     * @param  string  $template blade template to render slider
     * @return string rendered slider HTML
     */
    public function render($slider, string $template = 'slider::frontend.bootstrap.slider', $options = []): string
    {
        if (! $slider instanceof Slider) {
            $slider = $this->getSliderFromRepository($slider);
            if ($slider && $slider->active == false) {    // inactive slider must not render
                return '';
            }
        }
        if (! $slider) {
            return '';
        }

        $view = View::make($template)
            ->with([
                'slider' => $slider,
                'options' => $options,
            ]);

        return $view->render();
    }

    private function getSliderFromRepository($systemName): Slider
    {
        return $this->sliderRepository->findBySystemName($systemName);
    }
}
