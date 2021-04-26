<?php

namespace ChessableBanking\UserInterface\Panel\Model;

class MenuPosition
{

    private string $icon;
    private string $label;
    private string $url;

    public function __construct(string $icon, string $label, string $url)
    {
        $this->icon = $icon;
        $this->label = $label;
        $this->url = $url;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
