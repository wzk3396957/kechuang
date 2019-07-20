import pygame
from game_sprite import *

class PlaneMain(object):

    def __init__(self):
        # 游戏的初始化
        pygame.init()
        # 游戏窗口（x,y）
        self.screen = pygame.display.set_mode(SCREEN_RECT.size)
        # 创建时钟对象
        self.clock = pygame.time.Clock()
        # 3. 调用私有方法，精灵和精灵组的创建
        self.__create_sprites()
        # 4. 设置定时器事件 - 创建敌机　1s
        pygame.time.set_timer(CREATE_ENEMY_EVENT, 1000)
        pygame.time.set_timer(HERO_FIRE_EVENT, 500)
        # 5. 创建次数属性
        self.time = 0

    def startGame(self):

        while True:
            # 1. 设置刷新帧率
            self.clock.tick(60)
            # 2. 事件监听
            self.__event_handler()
            # 3. 碰撞检测
            self.__check_collide()
            # 4. 更新/绘制精灵组
            self.__update_sprites()
            # 5. 更新显示
            pygame.display.update()
            # 6. 执行的次数
            self.time += 1

    def __create_sprites(self):
        # 创建背景精灵和精灵组
        bg1 = Background()
        bg2 = Background(True)

        self.back_group = pygame.sprite.Group(bg1, bg2)

        # 创建敌机的精灵组
        self.enemy_group = pygame.sprite.Group()

        # 创建英雄的精灵和精灵组
        self.hero = Hero("./img/hero1.png")
        self.hero_group = pygame.sprite.Group(self.hero)
        #氮气
        self.nitrogen = Nitrogen("./img/nitrogen1.png")
        self.nitrogen_group = pygame.sprite.Group(self.nitrogen)


        # 创建敌机碰撞精灵组
        self.enemy_hit_group = pygame.sprite.Group()

    def __update_sprites(self):

        self.back_group.update()
        self.back_group.draw(self.screen)

        self.enemy_group.update()
        self.enemy_group.draw(self.screen)

        self.hero_group.update()
        self.hero_group.draw(self.screen)

        self.nitrogen_group.update()
        self.nitrogen_group.draw(self.screen)

        self.hero.bullets.update()
        self.hero.bullets.draw(self.screen)

    def __event_handler(self):

        for event in pygame.event.get():

            # 判断是否退出游戏
            if event.type == pygame.QUIT:
                PlaneMain.__game_over()
            elif event.type == CREATE_ENEMY_EVENT:
                # print("敌机出场...")
                # 创建敌机精灵
                enemy = Enemy()

                # 将敌机精灵添加到敌机精灵组
                self.enemy_group.add(enemy)
                # 飞机开火
            elif event.type == HERO_FIRE_EVENT:
                self.hero.fire()
            elif event.type == pygame.KEYDOWN and event.key == pygame.K_RIGHT:
                self.hero.image = pygame.image.load("./img/hero_right2.png")

            elif event.type == pygame.KEYDOWN and event.key == pygame.K_LEFT:
                self.hero.image = pygame.image.load("./img/hero_left2.png")

        if self.hero.explode_index == 0:



            # 使用键盘提供的方法获取键盘按键 - 按键元组
            keys_pressed = pygame.key.get_pressed()
            # 判断元组中对应的按键索引值 1
            if keys_pressed[pygame.K_RIGHT]:
                self.hero.image = pygame.image.load("./img/hero_right1.png")
                self.hero.speed = HERO_SPEED
                self.nitrogen.speed = HERO_SPEED
            elif keys_pressed[pygame.K_LEFT]:
                self.hero.image = pygame.image.load("./img/hero_left1.png")
                self.hero.speed = -HERO_SPEED
                self.nitrogen.speed = -HERO_SPEED
            else:
                self.hero.image = pygame.image.load("./img/hero1.png")
                self.hero.speed = 0
                self.nitrogen.speed = 0
            # 判断元组中对应的按键索引值 1
            if keys_pressed[pygame.K_DOWN]:
                self.hero.speed2 = HERO_SPEED
                self.nitrogen.speed2 = HERO_SPEED
            elif keys_pressed[pygame.K_UP]:
                self.hero.speed2 = -HERO_SPEED
                self.nitrogen.speed2 = -HERO_SPEED
            else:
                self.hero.speed2 = 0
                self.nitrogen.speed2 = 0
            # 刷新氮气
            self.nitrogen.rect.centerx = self.hero.rect.centerx
            self.nitrogen.rect.top = self.hero.rect.bottom - 10
            if self.time % 5 == 0:
                self.nitrogen.image = pygame.image.load("./img/nitrogen1.png")
            else:
                self.nitrogen.image = pygame.image.load("./img/nitrogen2.png")


        else:
            self.nitrogen.kill()

    def __check_collide(self):

        # 1. 子弹摧毁敌机
        # pygame.sprite.groupcollide(self.hero.bullets, self.enemy_group, True, True)
        # 子弹消灭敌机
        # 在敌机被消灭时显示爆炸过程
        # 敌机与子弹相撞时先不移除敌机精灵
        enemy_hit = pygame.sprite.groupcollide(self.enemy_group,self.hero.bullets, False, True)
        self.enemy_hit_group.add(enemy_hit)
        # print(self.enemy_hit_group)
        for enemy1 in self.enemy_hit_group:
            if enemy1.explode_index == 0:

                # 判断是否输出爆炸效果图
                enemy1.explode_index = 1

            # 判断在爆炸效果图输完后删除精灵
            elif enemy1.explode_index == 11:
                self.enemy_hit_group.remove_internal(enemy1)
                self.enemy_group.remove_internal(enemy1)

        # 2. 敌机撞毁英雄
        enemies = pygame.sprite.spritecollide(self.hero, self.enemy_group, True)

        # 判断列表时候有内容
        if len(enemies) > 0:

            # 让英雄牺牲
            #self.hero.kill()

            # 结束游戏
            #PlaneMain.__game_over()

            self.hero.explode_index = 1

    @staticmethod
    def __game_over():
        print("游戏结束")

        pygame.quit()
        exit()


game = PlaneMain()
game.startGame()
