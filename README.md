AntQaAjaxAutoCompleteBundle
===========================

Quick usage with select2:

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

Example routes:

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

Example controllers action:

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
                    ->where($expr->like('o.name', ':name')
                    ->setParameter('name', sprintf('%s%%', $request->query->get('q', '')))
                    ->getQuery()
                    ->getArrayResult();

        return new JsonResponse($objects);
    }

    public function get($ids)
    {
        $ids = explode(',', $ids);

        $objects = $this->getManager()
                    ->getRepository('Acme:Object')
                    ->createQueryBuilder('o')
                    ->select('o.id, o.name')
                    ->where($this->getExpr()->in('o.id', ':ids'))
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->getArrayResult();

        return new JsonResponse($objects);
    }

License
-------

See [Resources/meta/LICENSE](https://github.com/piotrantosik/AntQaAjaxAutoCompleteBundle/blob/master/Resources/meta/LICENSE).
