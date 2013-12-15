<?php
namespace StaticMock;


class Person {

    public function drive()
    {
        return Car::boo();
    }

    public function warn($times)
    {
        return Car::beep($times);
    }

}
