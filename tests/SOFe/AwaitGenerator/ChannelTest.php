<?php

/*
 * await-generator
 *
 * Copyright (C) 2018 SOFe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace SOFe\AwaitGenerator;

use PHPUnit\Framework\TestCase;

/**
 * @small
 */
class ChannelTest extends TestCase{
	public function testSendFirst() : void{
		/** @var Channel<string> $channel */
		$channel = new Channel;
		$clock = new MockClock;

		$eventCounter = 0;

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(1);
			yield from $channel->sendAndWait("a");
			self::assertSame(3, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(2);
			yield from $channel->sendAndWait("b");
			self::assertSame(5, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(3);
			$receive = yield from $channel->receive();
			self::assertSame(3, $clock->currentTick());
			self::assertSame("a", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(4);
			yield from $channel->sendAndWait("c");
			self::assertSame(6, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(5);
			$receive = yield from $channel->receive();
			self::assertSame(5, $clock->currentTick());
			self::assertSame("b", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(6);
			$receive = yield from $channel->receive();
			self::assertSame(6, $clock->currentTick());
			self::assertSame("c", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(7);
			$receive = yield from $channel->receive();
			self::assertSame(8, $clock->currentTick());
			self::assertSame("d", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(8);
			yield from $channel->sendAndWait("d");
			self::assertSame(8, $clock->currentTick());
			$eventCounter += 1;
		});

		$clock->nextTick(1);
		self::assertSame(0, $eventCounter);

		$clock->nextTick(2);
		self::assertSame(0, $eventCounter);

		$clock->nextTick(3);
		self::assertSame(2, $eventCounter);

		$clock->nextTick(4);
		self::assertSame(2, $eventCounter);

		$clock->nextTick(5);
		self::assertSame(4, $eventCounter);

		$clock->nextTick(6);
		self::assertSame(6, $eventCounter);

		$clock->nextTick(7);
		self::assertSame(6, $eventCounter);

		$clock->nextTick(8);
		self::assertSame(8, $eventCounter);
	}

	public function testReceiveFirst() : void{
		/** @var Channel<string> $channel */
		$channel = new Channel;
		$clock = new MockClock;

		$eventCounter = 0;

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(1);
			$receive = yield from $channel->receive();
			self::assertSame(3, $clock->currentTick());
			self::assertSame("a", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(2);
			$receive = yield from $channel->receive();
			self::assertSame(5, $clock->currentTick());
			self::assertSame("b", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(3);
			yield from $channel->sendAndWait("a");
			self::assertSame(3, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(4);
			$receive = yield from $channel->receive();
			self::assertSame(6, $clock->currentTick());
			self::assertSame("c", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(5);
			yield from $channel->sendAndWait("b");
			self::assertSame(5, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(6);
			yield from $channel->sendAndWait("c");
			self::assertSame(6, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(7);
			yield from $channel->sendAndWait("d");
			self::assertSame(8, $clock->currentTick());
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(8);
			$receive = yield from $channel->receive();
			self::assertSame(8, $clock->currentTick());
			self::assertSame("d", $receive);
			$eventCounter += 1;
		});

		$clock->nextTick(1);
		self::assertSame(0, $eventCounter);

		$clock->nextTick(2);
		self::assertSame(0, $eventCounter);

		$clock->nextTick(3);
		self::assertSame(2, $eventCounter);

		$clock->nextTick(4);
		self::assertSame(2, $eventCounter);

		$clock->nextTick(5);
		self::assertSame(4, $eventCounter);

		$clock->nextTick(6);
		self::assertSame(6, $eventCounter);

		$clock->nextTick(7);
		self::assertSame(6, $eventCounter);

		$clock->nextTick(8);
		self::assertSame(8, $eventCounter);
	}

	public function testNonBlockSend() : void {
		/** @var Channel<string> $channel */
		$channel = new Channel;
		$clock = new MockClock;

		$eventCounter = 0;

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(1);
			$channel->sendNonBlock("a");
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(2);
			$receive = yield from $channel->receive();
			self::assertSame(2, $clock->currentTick());
			self::assertSame("a", $receive);
			$eventCounter += 1;
		});


		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(3);
			$receive = yield from $channel->receive();
			self::assertSame(4, $clock->currentTick());
			self::assertSame("a", $receive);
			$eventCounter += 1;
		});

		Await::f2c(function() use($channel, $clock, &$eventCounter){
			yield from $clock->sleepUntil(4);
			$channel->sendNonBlock("a");
			$eventCounter += 1;
		});

		$clock->nextTick(1);
		self::assertSame(1, $eventCounter);

		$clock->nextTick(2);
		self::assertSame(2, $eventCounter);

		$clock->nextTick(3);
		self::assertSame(2, $eventCounter);

		$clock->nextTick(4);
		self::assertSame(4, $eventCounter);
	}
}