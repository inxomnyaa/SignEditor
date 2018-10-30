<?php

namespace xenialdan\SignEditor;

use pocketmine\block\WallSign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\tile\Sign as TileSign;
use pocketmine\utils\TextFormat;
use xenialdan\customui\elements\Input;
use xenialdan\customui\windows\CustomForm;

class EventListener implements Listener
{
    /** @var Loader */
    public $owner;

    public function __construct(Plugin $plugin)
    {
        $this->owner = $plugin;
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        if (!($block = $event->getBlock()) instanceof WallSign || !array_key_exists($event->getPlayer()->getName(), Loader::$editing)) return;
        /** @var TileSign $tile */
        if (!($tile = $block->getLevel()->getTile($block)) instanceof TileSign) return;
        $event->setCancelled();
        if (!$player->isOp()) return;
        $elements = [$tile];
        $elements = array_merge($elements, array_map(function ($text) {
            return new Input("Line", "Text", $text);
        }, $tile->getText()));
        $form = new CustomForm(TextFormat::BLACK . "Edit Sign");
        foreach ($elements as $element) {
            $form->addElement($element);
        };
        $form->setCallable(function (Player $player, $data) use ($tile) {
            Server::getInstance()->getPluginManager()->callEvent($ev = new SignChangeEvent($tile->getBlock(), $player, $data));
            if (!$ev->isCancelled()) {
                $tile->setText($ev->getLines());
                $player->sendMessage(TextFormat::GREEN . "Changed the sign contents");
            } else {
                $player->sendMessage(TextFormat::RED . "The change was cancelled");
            }
            unset(Loader::$editing[$player->getName()]);
        });
        $form->setCallableClose(function (Player $player) {
            $player->sendMessage(TextFormat::RED . "The form was closed without submitting, the sign will not be changed");
        });
        $player->sendForm($form);
    }
}