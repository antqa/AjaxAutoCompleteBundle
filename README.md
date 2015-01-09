AntQaAjaxAutoCompleteBundle
===========================

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Quality Score](https://img.shields.io/scrutinizer/g/piotrantosik/AntQaAjaxAutoCompleteBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/piotrantosik/AntQaAjaxAutoCompleteBundle)
[![Packagist Version](https://img.shields.io/packagist/v/piotrantosik/ajax-autocomplete-bundle.svg?style=flat-square)](https://packagist.org/packages/piotrantosik/ajax-autocomplete-bundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b7e7a961-c12b-4983-a3be-ae80c9e68a83/big.png)](https://insight.sensiolabs.com/projects/b7e7a961-c12b-4983-a3be-ae80c9e68a83)

Install
-------

AntQaAjaxAutoCompleteBundle is installed using Composer.

```bash
$ php composer.phar require piotrantosik/ajax-autocomplete-bundle
```

Enable bundle
-------------

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new AntQa\Bundle\AjaxAutoCompleteBundle\AntQaAjaxAutoCompleteBundle(),
    );
}
```

Example usage
-------

Quick usage with select2:

```javascript
    $('input').select2({
        multiple: true,
        minimumInputLength: 2,
        ajax: {
            url: '/find',
            dataType: 'json',
            data: function (term) {
                return {
                    q: term
                };
            },
            results: function (data) {
                return { results: data };
            }
        },
        formatResult: function(object) {
            return object.name;
        },
        formatSelection: function(object) {
            return object.name;
        },
        initSelection: function(element, callback) {
            var ids=$(element).val();
            if (ids !== '') {
                $.ajax('/get/' + ids, {
                    dataType: 'json'
                }).done(function(data) { callback(data); });
            }
        }
    });
```

Example routes:

```yaml
    find_objects:
        pattern:        /find
        defaults:       { _controller: Acme:Default:find }
        condition: "request.headers.get('X-Requested-With') matches '/XmlHttpRequest/i'"
        options:
            expose: true

    get_objects:
        pattern:        /get/{ids}
        defaults:       { _controller: Acme:Default:get }
        options:
            expose: true
```

Example controllers action:

```php
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Doctrine\ORM\Query\Expr;

    public function findAction(Request $request)
    {
        $expr = new Expr();

        $objects = $this->getManager()
                    ->getRepository('Acme:Object')
                    ->createQueryBuilder('o')
                    ->select('o.name, o.id')
                    ->where($expr->like('o.name', ':name'))
                    ->setParameter('name', sprintf('%s%%', $request->query->get('q', '')))
                    ->getQuery()
                    ->getArrayResult();

        return new JsonResponse($objects);
    }

    public function get($ids)
    {
        $expr = new Expr();
        $ids = explode(',', $ids);

        $objects = $this->getManager()
                    ->getRepository('Acme:Object')
                    ->createQueryBuilder('o')
                    ->select('o.id, o.name')
                    ->where($expr->in('o.id', ':ids'))
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->getArrayResult();

        return new JsonResponse($objects);
    }
```

License
-------

See [Resources/meta/LICENSE](https://github.com/piotrantosik/AntQaAjaxAutoCompleteBundle/blob/master/Resources/meta/LICENSE).
