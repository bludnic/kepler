<?php

namespace Framework\View;

interface View {
  public function render($template, $data = []);
  public function share($key, $value);
}
