Stream sampler
=============

Execute console application to see how it works:
```
php src/application.php sampler:sample
```

### Configuration of console script
Configuration file: config/sampler.yml

Supported kinds of input data:

1. Text
2. Random (values generated using openssl_random_pseudo_bytes)
3. RandomOrg (values loaded from http://www.random.org/clients/http/)
4. File


### TODO
1. Refine report in console application.
2. Test reading from big files drawing charts.
3. Improve UTF8 reading.
