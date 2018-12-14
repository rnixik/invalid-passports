#!/usr/bin/env tarantool

box.cfg {}

space = box.schema.space.create('invalid_passports')

box.space.invalid_passports:format({ {name = 'key', type = 'unsigned'} })

box.space.invalid_passports:create_index('primary', {
                                                        type = 'hash',
                                                        parts = {'key'}
                                                    })
