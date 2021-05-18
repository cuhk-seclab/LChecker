# LChecker

Weakly-typed languages support loosely comparing two operands by implicitly converting their types and values (*e.g.,* type juggling). Such loose comparison can cause unexpected program behaviours, namely *loose comparison bugs*. LChecker is a static analysis system for detecting loose comparison bugs in PHP programs. It employs a context-sensitive inter-procedural analysis to label loose comparison bugs. 

LChecher has been tested on Debian GNU/Linux 9.12 running PHP7. 

## Build

Use [composer](https://getcomposer.org/) to install the dependencies specified in `composer.json`. 

```sh
cd src/
composer install
```

## Run

LChecker directly analyzes the PHP source code and outputs results. 

```sh
cd src/
# To analyze a single PHP file, e.g., app.php
php Main.php app.php
# To analyze an entire PHP application at app/
php Main.php app/
```

## License

LChecker is under [MIT License](LICENSE).

## Publication

You can find more details in our [WWW 2021 paper](https://seclab.cse.cuhk.edu.hk/papers/www21_lchecker.pdf).

```tex
@inproceedings{li2021lchecker,
    title       = {LChecker: Detecting Loose Comparison Bugs in PHP},
    author      = {Li, Penghui and Meng, Wei},
    booktitle   = {Proceedings of The Web Conference 2021},
    month       = apr,
    year        = 2021
}
```

## Contacts

- Penghui Li (<phli@cse.cuhk.edu.hk>)
- Wei Meng (<wei@cse.cuhk.edu.hk>)

