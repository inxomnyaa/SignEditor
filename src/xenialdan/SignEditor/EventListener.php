<?php

namespace xenialdan\SignEditor;

use pocketmine\block\WallSign;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\form\CustomForm;
use pocketmine\form\element\CustomFormElement;
use pocketmine\form\element\Input;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\tile\Sign as TileSign;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{
	/** @var Loader */
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
	}

	public function onInteract(PlayerInteractEvent $event){
		if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		if (!($block = $event->getBlock()) instanceof WallSign || !array_key_exists($event->getPlayer()->getName(), Loader::$editing)) return;
		/** @var TileSign $tile */
		if (!($tile = $block->getLevel()->getTile($block)) instanceof TileSign) return;
		$event->setCancelled();
		if (!$player->isOp()) return;
		$elements = [$tile];
		$elements = array_merge($elements, array_map(function ($text){
			return new Input("Line", "Text", $text);
		}, $tile->getText()));
		$player->sendForm(new class(TextFormat::BLACK . "Edit Sign", $elements) extends CustomForm{
			/** @var TileSign $tile */
			private $tile;

			public function __construct($title, array $elements){
				$this->tile = array_shift($elements);
				parent::__construct($title, ...$elements);
			}

			public function onSubmit(Player $player): ?Form{
				$text = array_map(function (CustomFormElement $element){
					return $element->getValue();
				}, $this->getAllElements());
				$this->tile->setText(...$text);
				unset(Loader::$editing[$player->getName()]);
				return parent::onSubmit($player);
			}
		}, true);
	}
}