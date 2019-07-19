import pygame
import random

#屏幕
SCREEN_RECT = pygame.Rect(0, 0, 512, 768)
# 创建敌机的定时器常量
CREATE_ENEMY_EVENT = pygame.USEREVENT
# 英雄发射子弹事件
HERO_FIRE_EVENT = pygame.USEREVENT + 1
#英雄移动速度
HERO_SPEED = 4


class GameSprite(pygame.sprite.Sprite):
    def __init__(self,image_name,speed=1):
        #父类
        super().__init__()

        #定义对象的属性
        self.image = pygame.image.load(image_name)
        self.rect = self.image.get_rect()
        self.speed = speed

    def update(self):

        self.rect.y += self.speed


class Enemy(GameSprite):
    """敌机精灵"""

    def __init__(self):

        # 1. 调用父类方法，创建敌机精灵，同时指定敌机图片
        super().__init__("./img/enemy.png")

        # 2. 指定敌机的初始随机速度 1 ~ 3
        self.speed = random.randint(1, 6)

        # 3. 指定敌机的初始随机位置
        self.rect.bottom = 0

        max_x = SCREEN_RECT.width - self.rect.width
        self.rect.x = random.randint(0, max_x)
        # 4. 爆炸索引
        self.explode_index = 0

    def update(self):

        # 1. 调用父类方法，保持垂直方向的飞行
        super().update()

        # 2. 判断是否飞出屏幕，如果是，需要从精灵组删除敌机
        if self.rect.y >= SCREEN_RECT.height:
            # print("飞出屏幕，需要从精灵组删除...")
            # kill方法可以将精灵从所有精灵组中移出，精灵就会被自动销毁
            self.kill()

        # 销毁敌机
        if self.explode_index == 11:
            self.kill()

        # 敌机爆炸
        if self.explode_index != 0:
            new_rect = self.rect
            super().__init__("./img/boom%d.png" % self.explode_index)
            # print(self.explode_index)
            self.explode_index += 1
            self.rect = new_rect

    def __del__(self):
        # print("敌机挂了 %s" % self.rect)
        pass


class Background(GameSprite):
    """游戏背景精灵"""

    def __init__(self, is_alt=False):

        # 1. 调用父类方法实现精灵的创建(image/rect/speed)
        super().__init__("./img/bg1.jpg")

        # 2. 判断是否是交替图像，如果是，需要设置初始位置
        if is_alt:
            self.rect.y = -self.rect.height

    def update(self):

        # 1. 调用父类的方法实现
        super().update()

        # 2. 判断是否移出屏幕，如果移出屏幕，将图像设置到屏幕的上方
        if self.rect.y >= SCREEN_RECT.height:
            self.rect.y = -self.rect.height



class Hero(GameSprite):
    """英雄精灵"""

    def __init__(self,img):

        # 1. 调用父类方法，设置image&speed
        super().__init__(img, 0)

        # 2. 设置英雄的初始位置
        self.rect.centerx = SCREEN_RECT.centerx
        self.rect.bottom = SCREEN_RECT.bottom - 120

        # 3. 创建子弹的精灵组
        self.bullets = pygame.sprite.Group()
        # 4. Y轴速度
        self.speed2 = 0

    def update(self):

        # 英雄在水平方向移动
        self.rect.x += self.speed
        self.rect.y += self.speed2

        # 控制英雄不能离开屏幕(x轴)
        if self.rect.x < 0:
            self.rect.x = 0
        elif self.rect.right > SCREEN_RECT.right:
            self.rect.right = SCREEN_RECT.right
        # 控制英雄不能离开屏幕(y轴)
        if self.rect.y < 0:
            self.rect.y = 0
        elif self.rect.bottom > SCREEN_RECT.bottom:
            self.rect.bottom = SCREEN_RECT.bottom

    def fire(self):
        # print("发射子弹...")

        for i in (0, 1, 2):
            # 1. 创建子弹精灵
            bullet = Bullet()

            # 2. 设置精灵的位置
            bullet.rect.bottom = self.rect.y - i * 30
            bullet.rect.centerx = self.rect.centerx

            # 3. 将精灵添加到精灵组
            self.bullets.add(bullet)




class Bullet(GameSprite):
    """子弹精灵"""

    def __init__(self):

        # 调用父类方法，设置子弹图片，设置初始速度
        super().__init__("./img/bullet.png", -7)

    def update(self):

        # 调用父类方法，让子弹沿垂直方向飞行
        super().update()

        # 判断子弹是否飞出屏幕
        if self.rect.bottom < 0:
            self.kill()

    def __del__(self):
        # print("子弹被销毁...")
        pass


class Nitrogen(Hero):
    """氮气精灵"""
    def __init__(self,img):

        # 1. 调用父类方法，设置image&speed
        super().__init__(img)

        # 2. 设置英雄的初始位置
        self.rect.centerx = SCREEN_RECT.centerx
        self.rect.bottom = SCREEN_RECT.bottom - 80

