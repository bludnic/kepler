<?php

namespace Framework\View;

interface View {
  public function render($template, $data = []);
}
