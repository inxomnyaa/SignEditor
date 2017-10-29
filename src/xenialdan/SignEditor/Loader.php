<?php

namespace xenialdan\SignEditor;

use pocketmine\level\Location;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;


class Loader extends PluginBase{
	/** @var Loader */
	private static $instance = null;
	/** @var array */
	public static $editing;

	public function onLoad(){
		self::$instance = $this;
	}

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
	}

	/**
	 * Returns an instance of the plugin
	 * @return Loader
	 */
	public static function getInstance(){
		return self::$instance;
	}
}